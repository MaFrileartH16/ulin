<?php
	global $conn;
	require_once '../../database.php';
	require '../../libs/pdf/fpdf.php';
	include '../../libs/qrcode/qrlib.php';
	
	// Initialize FPDF object
	$pdf = new FPDF();
	$pdf->AddPage();
	$pdf->SetFont('Arial', 'B', 16);
	
	// Add brand name on top of the invoice
	$pdf->Cell(0, 10, 'Invoice Ulin', 0, 1, 'C');
	
	// Check if request method is POST
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// Retrieve data from the form
		$customerName = $_POST['full_name'];
		$customerEmail = $_POST['email'];
		$numberOfPeople = $_POST['number_of_people'];
		$totalPriceStr = $_POST['total_price'];
		$totalPrice = filter_var($totalPriceStr, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$signatureData = $_POST['signature'];
		
		// Determine tourId from $_GET['id']
		if (isset($_GET['id'])) {
			$tourId = $_GET['id'];
			
			// Query to get tour details
			$queryTour = 'SELECT name, location, price FROM 2230511102_tours WHERE id = :tourId';
			$stmtTour = $conn->prepare($queryTour);
			$stmtTour->bindParam(':tourId', $tourId, PDO::PARAM_INT);
			$stmtTour->execute();
			$tour = $stmtTour->fetch(PDO::FETCH_ASSOC);
			
			if (!$tour) {
				echo 'Tour details not found.';
				exit;
			}
		} else {
			// Handle if tourId is not available or invalid
			echo 'Parameter tourId not found.';
			exit;
		}
		
		// Handling signature data
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
		
		// Save signature path to database
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
			
			// Check if signature file exists before creating PDF
			if (file_exists($signatureFilePath)) {
				// Prepare PDF output with relevant information
				$pdf->SetFont('Times', 'B', 12);
				
				// Customer Information
				$pdf->Cell(0, 10, 'Customer Information', 0, 1, 'L');
				$pdf->Cell(40, 10, 'Customer Name:', 0, 0);
				$pdf->Cell(60, 10, $customerName, 0, 1);
				
				$pdf->Cell(40, 10, 'Email:', 0, 0);
				$pdf->Cell(60, 10, $customerEmail, 0, 1);
				
				$pdf->Ln(5); // Space
				
				// Tour Details
				$pdf->Cell(0, 10, 'Tour Details', 0, 1, 'L');
				$pdf->Cell(40, 10, 'Tour Name:', 0, 0);
				$pdf->Cell(60, 10, $tour['name'], 0, 1);
				
				$pdf->Cell(40, 10, 'Location:', 0, 0);
				$pdf->Cell(60, 10, $tour['location'], 0, 1);
				
				$pdf->Cell(40, 10, 'Price per Person:', 0, 0);
				$pdf->Cell(60, 10, 'IDR ' . number_format($tour['price'], 2), 0, 1);
				
				$pdf->Ln(5); // Space
				
				// Payment Details
				$pdf->Cell(0, 10, 'Payment Details', 0, 1, 'L');
				$pdf->Cell(40, 10, 'Number of People:', 0, 0);
				$pdf->Cell(60, 10, $numberOfPeople, 0, 1);
				
				$pdf->Cell(40, 10, 'Total Price:', 0, 0);
				$pdf->Cell(60, 10, 'IDR ' . number_format($totalPrice, 2), 0, 1);
				
				$pdf->Ln(10); // Space
				
				// Add signature to PDF (bottom-right corner)
				$pdf->Image($signatureFilePath, 150, 240, 40, 20);
				
				// Name below the signature
				$pdf->SetXY(150, 260); // Set X and Y position for name below the signature
				$pdf->Cell(0, 10, $customerName, 0, 1, 'R');
				
				// Output PDF to browser for download
				$uniqueFileName = uniqid() . '.pdf';
				$filePath = '../../invoices/' . $uniqueFileName;
				$pdf->Output('F', $filePath);
				
				// Update PDF URL to database
				$updateQuery = 'UPDATE 2230511102_invoices SET pdf_url = :pdfUrl WHERE id = :receiptId';
				$stmt = $conn->prepare($updateQuery);
				$stmt->bindParam(':pdfUrl', $uniqueFileName);
				$stmt->bindParam(':receiptId', $receiptId, PDO::PARAM_INT);
				$stmt->execute();
				
				// Generate QR code using PDF URL
				$qrDir = '../../qr_codes/';
				if (!is_dir($qrDir)) {
					mkdir($qrDir, 0755, true);
				}
				
				$qrFileName = uniqid() . '.png';
				$qrFilePath = $qrDir . $qrFileName;
				QRcode::png('https://jasapembuatanwebsite.online/ulin/invoices/' . $uniqueFileName, $qrFilePath);
				
				// Update QR code URL to database
				$updateQrQuery = 'UPDATE 2230511102_invoices SET qr_code_url = :qrCodeUrl WHERE id = :receiptId';
				$stmt = $conn->prepare($updateQrQuery);
				$stmt->bindParam(':qrCodeUrl', $qrFileName);
				$stmt->bindParam(':receiptId', $receiptId, PDO::PARAM_INT);
				$stmt->execute();
				
				header('Location: ' . $filePath);
				exit;
			} else {
				echo 'Failed to create PDF due to issues with the signature file.';
			}
		} else {
			echo 'Failed to save data to the database.';
		}
	}
?>
