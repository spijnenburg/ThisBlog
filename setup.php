<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<title>Setup | ThisBlog</title>
	<link rel="stylesheet" href="css/thema.css">
</head>
<body>
	<div class="container">
		<div id="banner">
			<div id="banner-content">
				<h1>Setup</h1>
			</div>
		</div>
		<div class="sidebar">
		<ul>
			<li><a href="index.php">Home</a></li>
		</ul>
	</div>
		<div class="blog-content">
		<?php
		include 'includes/db-connectie.php';
		include 'includes/error-handler.php';

		$formSent = false;
		if (isset($_POST['submit']) && $_POST['submit'] != "") {
			$formSent = true;
			$errmsg = "Er ging iets mis met de query van ";
			$queryCreateTableUsers = 	"CREATE TABLE users (
											ID INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
											name VARCHAR(32) NOT NULL,
											password VARCHAR(32) NOT NULL,
											email VARCHAR(50) NOT NULL,
											UNIQUE (email)
										)";
			$resultCreateTableUsers = mysqli_query($link, $queryCreateTableUsers) or trigger_error($errmsg . "users");
		
			$queryCreateTableBlogPosts = 	"CREATE TABLE blogposts (
												ID INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
												userID INT NOT NULL,
												date DATETIME NOT NULL,
												title VARCHAR(50),
												content TEXT
											)";
			$resultCreateTableBlogPosts = mysqli_query($link, $queryCreateTableBlogPosts) or trigger_error($errmsg . "blogposts");

		} ?>
			<p>Gebruik dit script om eenmaal de benodigde tabellen aan te maken. Verwijder dit script als de tabellen succesvol zijn aangemaakt.</p>
			<?php if ($formSent){ ?>
				<p>Tabellen zijn toegevoegd</p>
			<?php } else { ?>
				<form action="setup.php" method="post">
					<input type="submit" name="submit" class="inline-button" value="Verzenden">
				</form>
			<?php } ?>
			
			
		</div>
	</div>
</body>
</html>