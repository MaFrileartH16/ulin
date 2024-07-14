<?php
	global $conn;
	require_once '../../database.php';
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$id = $_GET['id'] ?? null;  // Ambil ID dari query parameter
		
		if ($id) {
			// Manual input data processing
			$name = $_POST['name'] ?? '';
			$location = $_POST['location'] ?? '';
			$price = $_POST['price'] ?? null;
			$image = $_FILES['image']['name'] ?? '';
			
			// Ensure price is a valid integer or null
			if ($price === '') {
				$price = null;
			}
			
			// Update existing record
			$stmt = $conn->prepare('SELECT image FROM 2230511102_tours WHERE id = :id');
			$stmt->bindParam(':id', $id);
			$stmt->execute();
			$oldImage = $stmt->fetchColumn();
			
			if ($image) {
				// Generate a unique name for the new image
				$uniqueImageName = uniqid('img_', true) . '.' . pathinfo($image, PATHINFO_EXTENSION);
				$imagesDirectory = '../../images';
				if (!is_dir($imagesDirectory)) {
					mkdir($imagesDirectory, 0777, true);
				}
				move_uploaded_file($_FILES['image']['tmp_name'], "$imagesDirectory/$uniqueImageName");
				
				// Delete the old image
				if ($oldImage && file_exists("$imagesDirectory/$oldImage")) {
					unlink("$imagesDirectory/$oldImage");
				}
			} else {
				$uniqueImageName = $oldImage; // Keep the old image name if no new image is uploaded
			}
			
			$stmt = $conn->prepare('UPDATE 2230511102_tours SET name = :name, location = :location, price = :price, image = :image WHERE id = :id');
			$stmt->bindParam(':id', $id);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':location', $location);
			if ($price === null) {
				$stmt->bindValue(':price', null, PDO::PARAM_NULL);
			} else {
				$stmt->bindParam(':price', $price);
			}
			$stmt->bindParam(':image', $uniqueImageName);
			$stmt->execute();
		}
		
		header('Location: ../../tours.php');
	}
?>
