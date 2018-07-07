<?php
// vars
$dbURL 			= (isset($_POST['db-url']) && $_POST['db-url'] != "") ? $_POST['db-url'] : "localhost";
$dbUsername 	= (isset($_POST['db-username'])) ? $_POST['db-username'] : null;
$dbPassword 	= (isset($_POST['db-password'])) ? $_POST['db-password'] : null;
$dataSubmitted 	= false;
$msg 			= null;
$data 			= null;
$dbExists 		= null;
$countTables 	= 0;

// functions
function getQueryUsers() {
	return "CREATE TABLE users (
				ID INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
				name VARCHAR(32) NOT NULL,
				password VARCHAR(32) NOT NULL,
				email VARCHAR(50) NOT NULL,
				UNIQUE (email)
			)";
}

function getQueryBlogposts() {
	return "CREATE TABLE blogposts (
				ID INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
				userID INT NOT NULL,
				date DATETIME NOT NULL,
				title VARCHAR(50),
				content TEXT
			)";
}
function getQueryCountTables() {
	return "SHOW TABLES FROM thisblog
			WHERE tables_in_thisblog LIKE 'users' OR
			tables_in_thisblog LIKE 'blogposts'";
}
function countTables() {
	return "";
}

function getLoginScript($host, $user, $password) {
	return "
	<?php
	\$host = '$host';
	\$user = '$user';
	\$password = '$password';
	\$database = 'thisblog';
	
	@\$link = mysqli_connect(\$host, \$user, \$password);
	
	if (\$link) {
		if (!mysqli_select_db(\$link, \$database)) {
			trigger_error('Kan niet verbinden met database');
			exit;
		}
	} else {
		trigger_error('Kan niet met server verbinden');
		exit;
	}
	?> ";
}
?>
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
		include 'includes/error-handler.php';
		
		// print msg

		if (isset($_POST['submit']) && $_POST['submit'] === "Verzenden") {
			// get form data
			if ($dbUsername != "" && $dbPassword != "") {
				// connect, error handler geeft al foutmelding bij geen verbinding.
				$link = mysqli_connect($dbURL, $dbUsername, $dbPassword);
				if ($link) {
					// rewrite db-connectie
					$data = getLoginScript($dbURL, $dbUsername, $dbPassword);
					if (file_put_contents("./includes/db-connectie.php", $data)) {
						// check if db extists, if so, delete
						$dbExists = mysqli_query($link, "SHOW DATABASES LIKE 'thisblog'");
						if (mysqli_num_rows($dbExists) === 1) {
							mysqli_query($link, "DROP DATABASE thisblog");
						}
						// create and select database
						mysqli_query($link, "CREATE DATABASE thisblog");
						mysqli_select_db($link, "thisblog");
						// create tabels
						mysqli_query($link, getQueryUsers());
						mysqli_query($link, getQueryBlogposts());
						// check if tables exist
						$countTables = mysqli_query($link, getQueryCountTables());
						if (mysqli_num_rows($countTables) === 2) {
							$dataSubmitted = !$dataSubmitted;
						} else {
							trigger_error("Er ging iets mis met het aanmaken van de database en tabellen");
						}
					} else {
						trigger_error("Connectiebestand is niet bijgewerkt.");
					}
				}
			} else {
				$msg = "Vul je gebruikersnaam en wachtwoord in.";
			}
		} else if (isset($_POST['submit']) && $_POST['submit'] === "Verwijder dit bestand") {
			unlink("setup.php");
			header("Location: index.php");
		}
		?>
		
		<?php if (!$dataSubmitted) { ?>
			<p><strong>Vul hier je MySQL inloggegevens in</strong></p>
			<?php if ($msg) print "<p class='red'>" . $msg . "</p>" ?>
			<form action="setup.php" method="post" class="form">
				<table>
					<tr>
						<td>Database URL</td>
						<td><input type="text" name="db-url" placeholder="default: localhost"></td>
					</tr>
					<tr>
						<td>Database Username</td>
						<td><input type="text" name="db-username"></td>
					</tr>
					<tr>
						<td>Database Password*</td>
						<td><input type="password" name="db-password"></td>
					</tr>	
					<tr>
						<td></td>
						<td><input type="submit" name="submit" class="button" value="Verzenden"></td>
					</tr>				
				</table>
			</form>
			<p>*) Het wachtwoord wordt onbeschermd opgeslagen in de broncode!</p>
		<?php } else { ?>
			<?php if ($msg) print "<p class='red'>" . $msg . "</p>" ?>
			<p>Installatie gereed. Wil je het installatiebestand verwijderen?</p>
			<form action="setup.php" method="post" class="form">
				<input type="submit" name="submit" class="inline-button special-btn" value="Verwijder dit bestand">
			</form>
		<?php } ?>
		
		</div>
	</div>
</body>
</html>