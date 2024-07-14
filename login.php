<?php
	global $conn;
	require_once 'database.php';
	
	$message = '';
	$script = '';
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		$query = 'SELECT * FROM 2230511102_users WHERE username = :username AND password = :password';
		$stmt = $conn->prepare($query);
		$stmt->bindParam(':username', $username);
		$stmt->bindParam(':password', $password);
		$stmt->execute();
		
		$user = $stmt->fetch();
		if ($user) {
			$message = 'Login successful';
			$script = "<script>
                        alert('$message');
                        window.location = 'dashboard.php';
                       </script>";
		} else {
			$message = 'Login failed';
		}
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
<body class="flex justify-center items-center">
<form class='bg-neutral-100 flex flex-col p-8 rounded-[32px]' method="post">
	<h3 class="text-center text-[26px] font-bold mb-8">
		Login
	</h3>
	
	<div class="flex flex-col gap-2 mb-4">
		<label for="username">Username</label>
		<input class="h-12 rounded-[32px] px-4" type="text" id="username" name="username" placeholder="Enter your username">
	</div>
	
	<div class="flex flex-col gap-2 mb-4">
		<label for='password'>Password</label>
		<input class="h-12 rounded-[32px] px-4" type="password" id='password' name='password'
		       placeholder="Enter your password">
	</div>
	
	<button type="submit" class="bg-blue-500 text-white rounded-[32px] h-12">Masuk akun</button>
</form>

<?php
	if (!empty($script)) {
		echo $script;
	} elseif (!empty($message)) {
		echo "<script>alert('$message');</script>";
	}
?>
</body>
</html>
