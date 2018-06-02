<?php
session_start(); 
if (isset($_SESSION['userID'])) header("Location: index.php");
?>
<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<title>Inloggen | ThisBlog</title>
	<link rel="stylesheet" href="css/thema.css">
</head>
<body>
	<div class="container">
		<div id="banner">
			<div id="banner-content">
				<h1>Inloggen</h1>
			</div>
		</div>
		<?php 
		include 'includes/db-connectie.php';
		include 'includes/error-handler.php';
		include 'includes/nav-menu.php'; 
		?>
		<div class="blog-content">
		<?php 

		$email = isset($_POST['email']) ? $_POST['email'] : NULL;
		$password = isset($_POST['password']) ? $_POST['password'] : NULL;

		if (isset($_POST['submit']) && $_POST['submit'] === "Inloggen") {
			if (!$email) {
				$invalidEmail = "<td class=\"red\">Vul een e-mailadres in.</td>";
			} elseif (!$password) {
				$invalidPassword = "<td class=\"red\">Vul een wachtwoord in.</td>";
			} else {
				$email = strtolower($email);
				$email = mysqli_real_escape_string($link, $email);
				$password = md5($password);
				$query = "SELECT ID, name, email FROM users WHERE email = '$email' AND password = '$password'";
				$result = mysqli_query($link, $query);
				if (mysqli_num_rows($result) === 1) {
					$row = mysqli_fetch_array($result);	
					if (strtolower($row['name']) === "admin") {
						$_SESSION['userID'] = 0;
					} else {
						$_SESSION['userID'] = (int) $row['ID'];	// query retourneert een string.
					}
					header("Location: index.php");
				} else {
					$msg = "<p class=\"red\">De combinatie e-mailadres en wachtwoord is onjuist.</p>";
				}				
			}
		}
		?>
			<p>Log in met je e-mailadres. Heb je nog geen account? <a href="registreren.php">Registreer hier.</a></p>
            <form action="inloggen.php" class="form" method="POST">
				<table>
					<tr>
						<td>E-mail</td>
						<td><input type="text" name="email" value="<?php print $email; ?>"></td>
						<?php if (isset($invalidEmail)) print $invalidEmail; ?>
					</tr>
					<tr>
						<td>Wachtwoord</td>
						<td><input type="password" name="password"></td>
						<?php if (isset($invalidPassword)) print $invalidPassword; ?>
					</tr>
					<tr>
						<td></td>
						<td>
							<input type="submit" name="submit" class="button" value="Inloggen">
						</td>
					</tr>
				</table>
            </form>
			<?php if (isset($msg)) print $msg; ?>
		</div>
	</div>
</body>
</html>