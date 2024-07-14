<?php
	
	$host = 'localhost';
	$dbname = 'jasf9527_kelasc';
//	$dbname = 'u341021167_kelasc';
	$username = 'jasf9527_kelasc';
//	$username = 'u341021167_kelasc123';
	$password = 'jasf9527_kelasc';
//	$password = 'Kelasc_123';
	
	try {
		$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
		// Set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		echo 'Connection failed: ' . $e->getMessage();
	}
