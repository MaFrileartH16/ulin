<?php
	global $conn;
	require_once 'database.php';
	
	try {
		$stmt = $conn->prepare('SELECT * FROM 2230511102_tours');
		$stmt->execute();
		$tours = $stmt->fetchAll();
	} catch (PDOException $e) {
		// Handle error
		die("Could not connect to the database $dbname :" . $e->getMessage());
	}
?>

<html lang='en'>
<head>
	<meta charset='UTF-8'>
	<meta name='viewport'
	      content='width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0'>
	<meta http-equiv='X-UA-Compatible' content='ie=edge'>
	<title>Ulin</title>
	
	<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css' />
	<script src='https://cdn.tailwindcss.com'></script>
</head>
<body class='flex'>
<aside
	class='flex flex-col justify-between w-20 border-r-2 border-neutral-100 sticky top-0 h-screen items-center py-4'>
	<i class='ti ti-route text-blue-500 text-3xl'></i>
	
	<nav class='flex flex-col gap-4'>
		<button onclick="window.location.href='dashboard.php'"
		        class='hover:bg-blue-500 text-blue-500 w-12 h-12 hover:text-white flex justify-center items-center rounded-[32px] cursor-pointer'>
			<i class='ti ti-home text-3xl'></i>
		</button>
		
		<button onclick="window.location.href='tours.php'"
		        class='bg-blue-500 text-blue-500 w-12 h-12 text-white flex justify-center items-center rounded-[32px] cursor-pointer'>
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

<main class='p-20 w-full'>
	<div class="flex justify-between">
		<h3 class='text-[26px] font-bold mb-8'>Tours</h3>
		
		<button onclick="window.location.href='tours/create.php'"
		        class='bg-blue-500 w-12 h-12 text-white flex justify-center items-center rounded-[32px] cursor-pointer'>
			<i class='ti ti-plus text-3xl'></i>
		</button>
	</div>
	
	<table class='border-collapse border border-neutral-200 table-auto'>
		<thead>
		<tr class='h-12'>
			<th class='px-4 border border-slate-200'>#</th>
			<th class='px-4 border border-slate-200'>Image</th>
			<th class='px-4 border border-slate-200'>Name</th>
			<th class='px-4 border border-slate-200'>Location</th>
			<th class='px-4 border border-slate-200'>Price</th>
			<th class='px-4 border border-slate-200'>Action</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($tours as $index => $tour): ?>
			<tr class='h-12'>
				<td class='px-4 border border-slate-200'><?= $index + 1 ?></td>
				<td class='px-4 border border-slate-200'>
					<img class='w-20 h-20 object-cover'
					     src='<?= strpos($tour['image'], "http") === 0 ? $tour['image'] : "images/" . $tour['image'] ?>'
					     alt='<?= $tour['name'] ?>'>
				</td>
				<td class='px-4 border border-slate-200'><?= $tour['name'] ?></td>
				<td class='px-4 border border-slate-200'><?= $tour['location'] ?></td>
				<td class='px-4 border border-slate-200'><?= $tour['price'] ?></td>
				<td class='px-4 border border-slate-200'>
					<div class='flex gap-2'>
						<form action='tours/edit.php?id=<?= $tour['id'] ?>' method='POST'>
							<button type='submit' class='bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded'>
								Edit
							</button>
						</form>
						<form action='functions/tour/destroy.php?id=<?= $tour['id'] ?>' method='POST'>
							<button type='submit' class='bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded'
							        onclick="return confirm('Are you sure?')">
								Delete
							</button>
						</form>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</main>
</body>
</html>
