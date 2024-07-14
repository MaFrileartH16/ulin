<?php
	global $conn;
	require_once 'database.php';
	
	// Check if the tour ID is provided
	if (!isset($_GET['id'])) {
		echo 'Tour ID is required.';
		exit; // Or redirect to another page
	}
	
	$tourId = $_GET['id'];
	
	try {
		$stmt = $conn->prepare('SELECT * FROM 2230511102_tours WHERE id = :id');
		$stmt->bindParam(':id', $tourId, PDO::PARAM_INT);
		$stmt->execute();
		
		// Check if the tour exists
		if ($stmt->rowCount() == 0) {
			echo 'Tour not found.';
			exit; // Or redirect
		}
		
		$tour = $stmt->fetch(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		echo 'Error: ' . $e->getMessage();
		exit;
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Tour Detail</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="max-w-4xl mx-auto p-5">
	<div class="bg-white shadow-lg rounded-lg overflow-hidden">
		<img class="w-full" src='<?= strpos($tour['image'], 'http') === 0 ? $tour['image'] : 'images/' . $tour['image'] ?>'
		     alt="Tour Image">
		<div class="p-4">
			<h2 class="text-2xl font-bold"><?= htmlspecialchars($tour['name']) ?></h2>
			<p class="text-gray-600">Location: <?= htmlspecialchars($tour['location']) ?></p>
			<p class="text-gray-600">Price (IDR) per person: <?= htmlspecialchars($tour['price']) ?></p>
		</div>
	</div>
	
	<div class='mt-5 bg-white shadow-lg rounded-lg p-4'>
		<h3 class='text-xl font-bold mb-4'>Buy Ticket</h3>
		
		<form action='functions/invoice/store.php?id=<?= $tourId ?>' method='post' class='space-y-3' id='ticketForm'
		      onsubmit="saveCanvas()">
			<div>
				<label for='full_name' class='block text-sm font-medium text-gray-700'>Your Full Name</label>
				<input type='text' name='full_name' id='full_name' placeholder='Your Full Name' required
				       class='mt-1 w-full px-3 py-2 border border-gray-300 rounded-md'>
			</div>
			<div>
				<label for='email' class='block text-sm font-medium text-gray-700'>Your Email</label>
				<input type='email' name='email' id='email' placeholder='Your Email' required
				       class='mt-1 w-full px-3 py-2 border border-gray-300 rounded-md'>
			</div>
			<div>
				<label for='number_of_people' class='block text-sm font-medium text-gray-700'>Number of People</label>
				<input type='number' name='number_of_people' id='number_of_people' placeholder='Number of People' required
				       min='1' class='mt-1 w-full px-3 py-2 border border-gray-300 rounded-md'>
			</div>
			<div>
				<label for='total_price' class='block text-sm font-medium text-gray-700'>Total Price (IDR)</label>
				<input type='text' name='total_price' id='total_price' placeholder='Total Price (IDR)' readonly
				       class='mt-1 w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-200'>
			</div>
			
			<div>
				<label for='signaturePad' class='block text-sm font-medium text-gray-700'>Signature</label>
				<canvas id='signaturePad' class='border border-gray-300 rounded-md' width='400' height='200'></canvas>
				<input type='hidden' name='signature' id='signature'>
				<button id='clearSignature' class='mt-2 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded'
				        type='button'>
					Clear Signature
				</button>
			</div>
			
			<div>
				<button type='submit' class='bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded'>
					Buy Now
				</button>
			</div>
		</form>
	</div>
	
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const numberOfPeopleInput = document.getElementById('number_of_people');
			const totalPriceInput = document.getElementById('total_price');
			const pricePerPerson = parseFloat(<?= json_encode($tour['price']) ?>);
			
			numberOfPeopleInput.addEventListener('input', function () {
				const numberOfPeople = parseInt(this.value, 10) || 0;
				const totalPrice = pricePerPerson * numberOfPeople;
				totalPriceInput.value = `${totalPrice.toLocaleString()} IDR`;
			});
		});
	</script>
	
	<script>
		const signaturePadCanvas = document.getElementById('signaturePad');
		const signaturePadContext = signaturePadCanvas.getContext('2d');
		let isDrawing = false;
		let lastX = 0;
		let lastY = 0;
		
		signaturePadCanvas.addEventListener('mousedown', (e) => {
			isDrawing = true;
			[lastX, lastY] = [e.offsetX, e.offsetY];
		});
		
		signaturePadCanvas.addEventListener('mousemove', (e) => {
			if (!isDrawing) return;
			signaturePadContext.beginPath();
			signaturePadContext.moveTo(lastX, lastY);
			signaturePadContext.lineTo(e.offsetX, e.offsetY);
			signaturePadContext.stroke();
			[lastX, lastY] = [e.offsetX, e.offsetY];
		});
		
		signaturePadCanvas.addEventListener('mouseup', () => isDrawing = false);
		signaturePadCanvas.addEventListener('mouseout', () => isDrawing = false);
		
		document.getElementById('clearSignature').addEventListener('click', () => {
			signaturePadContext.clearRect(0, 0, signaturePadCanvas.width, signaturePadCanvas.height);
		});
		
		// Set up drawing parameters
		signaturePadContext.strokeStyle = '#000000';
		signaturePadContext.lineWidth = 2;
	</script>
	
	<script>
		function saveCanvas() {
			var canvas = document.getElementById('signaturePad');
			var dataURL = canvas.toDataURL('image/png');
			document.getElementById('signature').value = dataURL;
		}
	</script>

</div>
</body>
</html>
