<?php
	global $conn;
	require_once 'database.php'; // Make sure this path is correct
	
	try {
		// Fetch total tours
		$stmtTours = $conn->prepare('SELECT COUNT(*) AS total_tours FROM 2230511102_tours');
		$stmtTours->execute();
		$resultTours = $stmtTours->fetch();
		$totalTours = $resultTours['total_tours'];
		
		// Fetch total invoices
		$stmtInvoices = $conn->prepare('SELECT COUNT(*) AS total_invoices FROM 2230511102_invoices'); // Replace 'invoices' with your actual table name
		$stmtInvoices->execute();
		$resultInvoices = $stmtInvoices->fetch();
		$totalInvoices = $resultInvoices['total_invoices'];
	} catch (PDOException $e) {
		echo 'Error: ' . $e->getMessage();
		$totalTours = 0; // Default to 0 in case of an error with tours
		$totalInvoices = 0; // Default to 0 in case of an error with invoices
	}
?>

<html lang="en">
<head>
	<meta charset='UTF-8'>
	<meta name='viewport'
	      content='width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0'>
	<meta http-equiv='X-UA-Compatible' content='ie=edge'>
	<title>Ulin</title>
	
	<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css' />
	<script src='https://cdn.tailwindcss.com'></script>
</head>
<body class="flex">
<aside
	class="flex flex-col justify-between w-20 border-r-2 border-neutral-100 sticky top-0 h-screen items-center py-4">
	<i class='ti ti-route text-blue-500 text-3xl'></i>
	
	<nav class="flex flex-col gap-4">
		<button onclick="window.location.href='dashboard.php'"
		        class="bg-blue-500 w-12 h-12 text-white flex justify-center items-center rounded-[32px] cursor-pointer">
			<i class='ti ti-home text-3xl'></i>
		</button>
		
		<button onclick="window.location.href='tours.php'"
		        class='hover:bg-blue-500 text-blue-500 w-12 h-12 hover:text-white flex justify-center items-center rounded-[32px] cursor-pointer'>
			<i class='ti ti-layout text-3xl'></i>
		</button>
		
		<button onclick="window.location.href='invoices.php'"
		        class='hover:bg-blue-500 text-blue-500 w-12 h-12 hover:text-white flex justify-center items-center rounded-[32px] cursor-pointer'>
			<i class='ti ti-invoice text-3xl'></i>
		</button>
	</nav>
	
	<button
		onclick="window.location.href='login.php'"
		class='hover:bg-red-500 text-red-500 w-12 h-12 hover:text-white flex justify-center items-center rounded-[32px] cursor-pointer'>
		<i class='ti ti-logout text-3xl cursor-pointer'></i>
	</button>
</aside>

<main class="p-20 w-full">
	<h3 class="text-[26px] font-bold mb-8">Dashboard</h3>
	
	<div class="grid grid-cols-2 gap-4">
		<div class='bg-neutral-100 p-8 rounded-[32px]'>
			<h3>Total Tours</h3>
			<p class='text-[26px] font-bold'><?php echo $totalTours; ?></p>
		</div>
		<div class="bg-neutral-100 p-8 rounded-[32px]">
			<h3>Total Invoices</h3>
			<p class='text-[26px] font-bold'><?php echo $totalInvoices; ?></p>
		</div>
	</div>
</main>
</body>
</html>
