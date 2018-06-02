<?php 
session_start();
session_destroy();
unset($_SESSION);
?>
<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<title>Uitloggen | ThisBlog</title>
	<link rel="stylesheet" href="css/thema.css">
</head>
<body>
	<div class="container">
		<div id="banner">
			<div id="banner-content">
				<h1>Uitloggen</h1>
			</div>
		</div>
		<div class="sidebar">
		<ul>
			<li><a href="index.php">Home</a></li>
			<li><a href="inloggen.php">Inloggen</a></li>
			<li><a href="registreren.php">Registreren</a></li>
		</ul>
	</div>
		<div class="blog-content">
			<p>Je bent succesvol uitgelogd.</p>
			<p>Klik hier om terug te gaan naar <a href="index.php">Home</a>.</p>
		</div>
	</div>
</body>
</html>