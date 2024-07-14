<?php
	global $conn;
	require_once '../../database.php';
	require '../../libs/pdf/fpdf.php';
	include '../../libs/qrcode/qrlib.php';
	
	// Inisialisasi objek FPDF
	$pdf = new FPDF();
	$pdf->AddPage();
	$pdf->SetFont('Arial', 'B', 16);
	
	// Tambahkan nama brand di atas invoice
	$pdf->Cell(0, 10, 'Invoice Ulin', 0, 1, 'C');
	
	// Memeriksa jika metode permintaan adalah POST
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// Mengambil data dari formulir
		$customerName = $_POST['full_name'];
		$customerEmail = $_POST['email'];
		$numberOfPeople = $_POST['number_of_people'];
		$totalPriceStr = $_POST['total_price'];
		$totalPrice = filter_var($totalPriceStr, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$signatureData = $_POST['signature'];
		
		// Menentukan tourId dari parameter $_GET['id']
		if (isset($_GET['id'])) {
			$tourId = $_GET['id'];
			
			// Query untuk mendapatkan detail tour
			$queryTour = 'SELECT name, location, price FROM 2230511102_tours WHERE id = :tourId';
			$stmtTour = $conn->prepare($queryTour);
			$stmtTour->bindParam(':tourId', $tourId, PDO::PARAM_INT);
			$stmtTour->execute();
			$tour = $stmtTour->fetch(PDO::FETCH_ASSOC);
			
			if (!$tour) {
				echo 'Detail tour tidak ditemukan.';
				exit;
			}
		} else {
			// Handle jika tourId tidak tersedia atau tidak valid
			echo 'Parameter tourId tidak ditemukan.';
			exit;
		}
		
		// Penanganan data tanda tangan
		$signatureData = str_replace('data:image/png;base64,', '', $signatureData);
		$signatureData = str_replace(' ', '+', $signatureData);
		$decodedData = base64_decode($signatureData);
		
		$signatureDir = '../../signatures/';
		if (!is_dir($signatureDir)) {
			mkdir($signatureDir, 0755, true);
		}
		
		$signatureName = uniqid() . '.png';
		$signatureFilePath = $signatureDir . $signatureName;
		file_put_contents($signatureFilePath, $decodedData);
		
		// Simpan path tanda tangan ke database
		$query = "INSERT INTO 2230511102_invoices (customer_name, customer_email, number_of_people, total_price, signature_url, pdf_url, qr_code_url, tour_id) VALUES (:customerName, :customerEmail, :numberOfPeople, :totalPrice, :signatureName, '', '', :tourId)";
		$stmt = $conn->prepare($query);
		$stmt->bindParam(':customerName', $customerName);
		$stmt->bindParam(':customerEmail', $customerEmail);
		$stmt->bindParam(':numberOfPeople', $numberOfPeople, PDO::PARAM_INT);
		$stmt->bindParam(':totalPrice', $totalPrice);
		$stmt->bindParam(':signatureName', $signatureName);
		$stmt->bindParam(':tourId', $tourId, PDO::PARAM_INT);
		
		if ($stmt->execute()) {
			$receiptId = $conn->lastInsertId();
			
			// Memeriksa keberadaan file tanda tangan sebelum membuat PDF
			if (file_exists($signatureFilePath)) {
				// Menyiapkan output PDF dengan informasi yang relevan
				$pdf->SetFont('Times', 'B', 12);
				
				// Informasi Pelanggan
				$pdf->Cell(0, 10, 'Informasi Pelanggan', 0, 1, 'L');
				$pdf->Cell(40, 10, 'Nama Pelanggan:', 0, 0);
				$pdf->Cell(60, 10, $customerName, 0, 1);
				
				$pdf->Cell(40, 10, 'Email:', 0, 0);
				$pdf->Cell(60, 10, $customerEmail, 0, 1);
				
				$pdf->Ln(5); // Spasi
				
				// Informasi Tour
				$pdf->Cell(0, 10, 'Detail Tour', 0, 1, 'L');
				$pdf->Cell(40, 10, 'Nama Wisata:', 0, 0);
				$pdf->Cell(60, 10, $tour['name'], 0, 1);
				
				$pdf->Cell(40, 10, 'Lokasi:', 0, 0);
				$pdf->Cell(60, 10, $tour['location'], 0, 1);
				
				$pdf->Cell(40, 10, 'Harga per Orang:', 0, 0);
				$pdf->Cell(60, 10, 'IDR ' . number_format($tour['price'], 2), 0, 1);
				
				$pdf->Ln(5); // Spasi
				
				// Detail Pembayaran
				$pdf->Cell(0, 10, 'Detail Pembayaran', 0, 1, 'L');
				$pdf->Cell(40, 10, 'Jumlah Orang:', 0, 0);
				$pdf->Cell(60, 10, $numberOfPeople, 0, 1);
				
				$pdf->Cell(40, 10, 'Total Harga:', 0, 0);
				$pdf->Cell(60, 10, 'IDR ' . number_format($totalPrice, 2), 0, 1);
				
				$pdf->Ln(10); // Spasi
				
				// Menambahkan tanda tangan ke PDF (bottom-right corner)
				$pdf->Image($signatureFilePath, 150, 240, 40, 20);
				
				// Nama di bawah tanda tangan
				$pdf->SetXY(150, 260); // Atur posisi X dan Y untuk nama di bawah tanda tangan
				$pdf->Cell(0, 10, $customerName, 0, 1, 'R');
				
				// Output PDF ke browser untuk diunduh
				$uniqueFileName = uniqid() . '.pdf';
				$filePath = '../../invoices/' . $uniqueFileName;
				$pdf->Output('F', $filePath);
				
				// Update URL PDF ke database
				$updateQuery = 'UPDATE 2230511102_invoices SET pdf_url = :pdfUrl WHERE id = :receiptId';
				$stmt = $conn->prepare($updateQuery);
				$stmt->bindParam(':pdfUrl', $uniqueFileName);
				$stmt->bindParam(':receiptId', $receiptId, PDO::PARAM_INT);
				$stmt->execute();
				
				// Generate QR code menggunakan URL PDF
				$qrDir = '../../qr_codes/';
				if (!is_dir($qrDir)) {
					mkdir($qrDir, 0755, true);
				}
				
				$qrFileName = uniqid() . '.png';
				$qrFilePath = $qrDir . $qrFileName;
				QRcode::png('https://jasapembuatanwebsite.online/ulin/invoices/' . $uniqueFileName, $qrFilePath);
				
				// Update URL QR code ke database
				$updateQrQuery = 'UPDATE 2230511102_invoices SET qr_code_url = :qrCodeUrl WHERE id = :receiptId';
				$stmt = $conn->prepare($updateQrQuery);
				$stmt->bindParam(':qrCodeUrl', $qrFileName);
				$stmt->bindParam(':receiptId', $receiptId, PDO::PARAM_INT);
				$stmt->execute();
				
				header('Location: ' . $filePath);
				exit;
			} else {
				echo 'Gagal membuat PDF karena ada masalah dengan file tanda tangan.';
			}
		} else {
			echo 'Gagal menyimpan data ke database.';
		}
	}
?>
