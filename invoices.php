<?php
	require_once 'database.php';
	
	try {
		$stmt = $conn->prepare('SELECT id, customer_name, customer_email, qr_code_url, created_at FROM 2230511102_invoices');
		$stmt->execute();
		$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		// Handle error
		die('Could not connect to the database: ' . $e->getMessage());
	}
?>

<!DOCTYPE html>
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
<aside class="flex flex-col justify-between w-20 border-r-2 border-neutral-100 sticky top-0 h-screen items-center py-4">
	<i class="ti ti-route text-blue-500 text-3xl"></i>
	
	<nav class="flex flex-col gap-4">
		<button onclick="window.location.href='dashboard.php'"
		        class="hover:bg-blue-500 text-blue-500 w-12 h-12 hover:text-white flex justify-center items-center rounded-[32px] cursor-pointer">
			<i class="ti ti-home text-3xl"></i>
		</button>
		
		<button onclick="window.location.href='tours.php'"
		        class="hover:bg-blue-500 text-blue-500 w-12 h-12 hover:text-white flex justify-center items-center rounded-[32px] cursor-pointer">
			<i class="ti ti-layout text-3xl"></i>
		</button>
		
		<button onclick="window.location.href='invoices.php'"
		        class="bg-blue-500 text-blue-500 w-12 h-12 text-white flex justify-center items-center rounded-[32px] cursor-pointer">
			<i class="ti ti-invoice text-3xl"></i>
		</button>
	</nav>
	
	<button onclick="window.location.href='login.php'"
	        class="hover:bg-red-500 text-red-500 w-12 h-12 hover:text-white flex justify-center items-center rounded-[32px] cursor-pointer">
		<i class="ti ti-logout text-3xl cursor-pointer"></i>
	</button>
</aside>

<main class="p-20 w-full">
	<h3 class="text-[26px] font-bold mb-8">Invoices</h3>
	
	<table class="border-collapse border border-neutral-200 table-auto">
		<thead>
		<tr class="h-12">
			<th class="px-4 border border-slate-200">#</th>
			<th class="px-4 border border-slate-200">Invoice ID</th>
			<th class="px-4 border border-slate-200">Customer Name</th>
			<th class="px-4 border border-slate-200">Email</th>
			<th class="px-4 border border-slate-200">Purchase Date</th>
			<th class="px-4 border border-slate-200">QR Code</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($invoices as $index => $invoice): ?>
			<tr class="h-12">
				<td class="px-4 border border-slate-200"><?= $index + 1 ?></td>
				<td class="px-4 border border-slate-200">#<?= $invoice['id'] ?></td>
				<td class="px-4 border border-slate-200"><?= $invoice['customer_name'] ?></td>
				<td class="px-4 border border-slate-200"><?= $invoice['customer_email'] ?></td>
				<td class="px-4 border border-slate-200"><?= $invoice['created_at'] ?></td>
				<td class="px-4 border border-slate-200">
					<img src="qr_codes/<?= $invoice['qr_code_url'] ?>" alt="QR Code">
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</main>
</body>
</html>
