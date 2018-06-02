<?php 
session_start(); 
?>
<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<title>ThisBlog</title>
	<link rel="stylesheet" href="css/thema.css">
</head>
<body>
	<div class="container">
		<div id="banner">
			<div id="banner-content">
				<h1>Welkom bij ThisBlog</h1>
			</div>
		</div>
		<?php 
		include 'includes/db-connectie.php';
		include 'includes/error-handler.php';

		/**
		 * Script om te kijken of tabellen bestaan
		 * Wordt eenmaal aangeroepen dus hoeft geen functie te zijn.
		 */

		$numTables = 0;
		$tableUsers = "users";
		$tableBlogPosts = "blogposts";
		
		$tableQuery = "SHOW TABLES";
		$tableResult = mysqli_query($link, $tableQuery);
		while ($row = mysqli_fetch_row($tableResult)) {
			if ($row[0] === $tableUsers) {
				$numTables++;
				$tableUsers = "";
			}
			if ($row[0] === $tableBlogPosts) {
				$numTables++;
				$tableBlogPosts = "";
			}
		}
		
		if ($numTables === 0) {
			$tablesMessage = "De tabellen $tableUsers en $tableBlogPosts ontbreken in de database";
			trigger_error($tablesMessage);
		} elseif ($numTables === 1) {
			$tablesMessage = "De tabel " . $tableUsers . $tableBlogPosts . " onbreekt in de database";
			trigger_error($tablesMessage);
		} else {
		include 'includes/nav-menu.php'; 
		?>
		<div class="blog-content">
			<p>Welkom bij ThisBlog. Links staat een lijst met alle blogs die je kunt lezen. Of klik op inloggen of registreren en maak je eigen blog.</p>
			<p>Ben je eenmaal ingelogd, dan kun je het menu op Bewerken klikken. Hier kun je nieuwe posts schrijven of bestaande posts aanpassen of verwijderen.</p>
		</div>
		<?php
		}
		?>
	</div>
</body>
</html>