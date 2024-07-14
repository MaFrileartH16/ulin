<?php
	global $conn;
	require_once 'database.php';
	
	$stmt = $conn->prepare('SELECT * FROM 2230511102_tours');
	$stmt->execute();
	$tours = $stmt->fetchAll();
?>

<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
	      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Ulin</title>
	
	<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css' />
	<script src='https://cdn.tailwindcss.com'></script>

</head>
<body>
<header class="h-20 flex justify-between gap-4 border-b-2 border-neutral-100 px-20 items-center sticky top-0 bg-white">
	<div class="flex items-center gap-2">
		<i class='ti ti-route text-blue-500 text-3xl'></i>
		<h3 class="font-bold" style="font-size: 26px">Ulin</h3>
	</div>
	
	<div class="text-end">
		<button onclick="window.location.href='login.php'" class="bg-blue-500 text-white h-12 rounded-[32px] px-4">Login
		</button>
		<!--		<button class="hover:bg-blue-500 border-solid border-2 border-blue-500 hover:text-white h-12 rounded-[32px] px-4">-->
		<!--			Register-->
		<!--		</button>-->
	</div>
</header>

<main class="px-20">
	<section class="py-20 grid grid-cols-2 gap-20">
		<div class="flex justify-center items-start flex-col">
			<h1 class="text-[44px] font-black">Welcome to Ulin</h1>
			<p class="text-lg text-neutral-500">The best place to find your next adventure</p>
		</div>
		
		<img
			class="rounded-[32px]"
			src="https://images.unsplash.com/photo-1569949381669-ecf31ae8e613?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
			alt="">
	</section>
	
	<section class='py-20'>
		<h2 class='text-center text-[34px] font-extrabold mb-12'>
			Discover the world
		</h2>
		
		<div class='grid grid-cols-4 gap-4'>
			<?php foreach ($tours as $tour): ?>
				<a href="detail.php?id=<?= htmlspecialchars($tour['id']) ?>" class="block">
					<div class="cursor-pointer">
						<img
							class="rounded-[32px] mb-2"
							src='<?= strpos($tour['image'], 'http') === 0 ? $tour['image'] : 'images/' . $tour['image'] ?>'
							alt="<?= htmlspecialchars($tour['name']) ?>">
						<h3 class="text-lg font-bold"><?= htmlspecialchars($tour['name']) ?></h3>
						<p class="text-neutral-500"><?= htmlspecialchars($tour['location']) ?></p>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</section>
</main>

<footer class="h-20 flex justify-center items-center bg-neutral-100 text-neutral-500">
	<p>&copy; 2024 Ulin. All rights reserved.</p>
</body>
</html>

