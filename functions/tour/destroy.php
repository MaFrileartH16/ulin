<?php
	global $conn;
	require_once '../../database.php';
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$id = $_GET['id'] ?? null; // Ambil ID dari query parameter
		
		if ($id) {
			// Retrieve the current image name to delete it from the server
			$stmt = $conn->prepare('SELECT image FROM 2230511102_tours WHERE id = :id');
			$stmt->bindParam(':id', $id);
			$stmt->execute();
			$imageName = $stmt->fetchColumn();
			
			if ($imageName && file_exists("../../images/$imageName")) {
				unlink("../../images/$imageName"); // Delete the image file
			}
			
			// Delete the record from the database
			$stmt = $conn->prepare('DELETE FROM 2230511102_tours WHERE id = :id');
			$stmt->bindParam(':id', $id);
			$stmt->execute();
			
			// Redirect after deletion
			header('Location: ../../tours.php');
		}
	}
?>
