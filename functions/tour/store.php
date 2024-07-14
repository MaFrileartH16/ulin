<?php
	global $conn;
	require_once '../../database.php';
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (isset($_FILES['excelFile']['name']) && $_FILES['excelFile']['name'] != '') {
			$excelFilePath = $_FILES['excelFile']['tmp_name'];
			$allData = [];
			
			if (($handle = fopen($excelFilePath, 'r')) !== FALSE) {
				fgetcsv($handle); // Skip header
				
				while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
					$originalImageName = $data[0]; // Use the original image name directly
					$name = $data[1];
					$location = $data[2];
					$price = $data[3];
					
					$allData[] = [$name, $location, $price, $originalImageName];
				}
				
				fclose($handle);
				
				$stmt = $conn->prepare('INSERT INTO 2230511102_tours (name, location, price, image) VALUES (:name, :location, :price, :image)');
				
				foreach ($allData as $row) {
					$stmt->bindParam(':name', $row[0]);
					$stmt->bindParam(':location', $row[1]);
					$stmt->bindParam(':price', $row[2]);
					$stmt->bindParam(':image', $row[3]);
					$stmt->execute();
				}
				
				header('Location: ../../tours.php');
			}
		} else {
			// Manual input data processing with random image name generation
			$name = $_POST['name'] ?? '';
			$location = $_POST['location'] ?? '';
			$price = $_POST['price'] ?? '';
			$image = $_FILES['image']['name'] ?? '';
			
			if ($image) {
				// Generate a unique name for the image
				$uniqueImageName = uniqid('img_', true) . '.' . pathinfo($image, PATHINFO_EXTENSION);
				$imagesDirectory = '../../images';
				if (!is_dir($imagesDirectory)) {
					mkdir($imagesDirectory, 0777, true);
				}
				move_uploaded_file($_FILES['image']['tmp_name'], "$imagesDirectory/$uniqueImageName");
			} else {
				$uniqueImageName = ''; // Handle case where no image is uploaded
			}
			
			$stmt = $conn->prepare('INSERT INTO 2230511102_tours (name, location, price, image) VALUES (:name, :location, :price, :image)');
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':location', $location);
			$stmt->bindParam(':price', $price);
			$stmt->bindParam(':image', $uniqueImageName);
			$stmt->execute();
			
			header('Location: ../../tours.php');
		}
	}
?>
