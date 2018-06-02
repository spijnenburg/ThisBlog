<?php 
session_start();

/**
 * validateEmail. Controleert een e-mailadres op diverse punten.
 *
 * Eisen aan emailadres:
 * Naam en domein moeten minimaal 2 letters bevatten.
 * Naam en domein bevatten geen cijfers of speciale tekens.
 * Het e-mailadres bevat een @.
 * Het e-maildres eindigt op .nl
 * Deze functie is niet hoofdlettergevoelig.
 *
 * @return integer 0 bij false, 1 bij true
*/
function validateEmail ($email) {
	$regex = '/^[a-z][a-z]+@[a-z][a-z]+\.nl$/i';
	$result = preg_match($regex, $email);
	return $result;
}

/**
 * Maak een wachtwoord met minimaal 1 kleine letter, 1 hoofdletter, 1 cijfer en 1 leesteken.
 * Minimale wacthwoordgrootte is 8.
 */
function randomChar($string){
	$length = strlen($string);
	$pos = mt_rand(0, $length - 1);
	if (mt_rand(0,1) === 1) $string = strtoupper($string);	// flip a coin. 0 means lowercase, 1 means uppercase
	return $string[$pos];
}

function generatePassword($length) {
	mt_srand((double) microtime() * 1000000);				// Seed RNG
	if ($length < 8) $length = 8;							// input validation
	$returnString = "";

	$alphabet = "abcdefghijklmnopqrstuvwxyz";
	$numbers = "0123456789";
	$special = "!@#$%^&*(){}[]?";
	$pool = $alphabet . $numbers . $special;

	$returnString .= randomChar(strtolower($alphabet)); 	// add one lower case letter
	$returnString .= randomChar(strtoupper($alphabet));		// add one upper case letter
	$returnString .= randomChar($numbers);					// add one number
	$returnString .= randomChar($special);					// add one special character

	// add rest of password
	for ($i = 0; $i < $length - 4; $i++) {
		$returnString .= randomChar($pool);
	}
	$returnString = str_shuffle($returnString);				// don't forget to shuffle
	return $returnString;
}

if (isset($_SESSION['userID'])) header("Location: index.php");
?>
<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<title>Registreren | ThisBlog</title>
	<link rel="stylesheet" href="css/thema.css">
</head>
<body>
	<div class="container">
		<div id="banner">
			<div id="banner-content">
				<h1>Registreren</h1>
			</div>
		</div>
		<?php 
		include 'includes/db-connectie.php';
		include 'includes/error-handler.php';
		include 'includes/nav-menu.php'; 
		?>
		<div class="blog-content">
		<?php

		$name = isset($_POST['name']) ? trim($_POST['name']) : NULL;
		$email = isset($_POST['email']) ? trim(strtolower($_POST['email'])) : NULL;
		$password = generatePassword(8);

		if (isset($_POST['submit']) && $_POST['submit'] === "Registreren") {
			if (!$name || strlen($name) > 32) {
				$invalidName = "<td class=\"red\">Vul een naam in. Max 32 karakters.</td>";
			} elseif (!$email || strlen($email) > 50 || ($email && validateEmail($email) === 0)) {
				$invalidEmail = "<td class=\"red\">Vul een correct e-mailadres in. Max 50 karakters.</td>";
			} else {

				$name = mysqli_real_escape_string($link, $name);
				$passwordHashed = md5($password);
				
				/**
				 * In de requirements staat vermeld dat de gebruiker moet inloggen met zijn e-mailadres als username.
				 * E-mailadres moet dus uniek zijn.
				 * Controleer of gebruiker al bestaat.
				 */
				$queryGetUser = "SELECT email FROM users WHERE email = '$email'";
				$resultGetUser = mysqli_query($link, $queryGetUser);

				/**
				 * Naar aanleiding van de feedback worden nu eerst de gebruikersgegevens in de database gezet.
				 * Hierna wordt de e-mail verzonden.
				 * Mislukt het mailen van wordt de gebruiker verzocht contact op te nemen met de administrator.
				 * De gebruiker kan namelijk niet onder hetzelfde adres een nieuwe account aanmaken
				 */
				if (mysqli_num_rows($resultGetUser) === 0) {

					 // Is de naam gelijk aan admin, dan wordt gecontroleerd of er al een gebruiker is met deze naam
					 $adminExists = false;
					
					if (strtolower($name) === "admin") {
						$queryAdminExists = "SELECT ID FROM users WHERE name = LOWER('$name')";
						$resultAdminExists = mysqli_query($link, $queryAdminExists);
						if (mysqli_num_rows($resultAdminExists) > 0) {
							$adminExists = true;
						}
					}

					if (!$adminExists) {
						$queryInsertUser = "INSERT INTO users VALUES (NULL, '$name', '$passwordHashed', '$email')";
						$resultInsertUser = mysqli_query($link, $queryInsertUser);
						if (mysqli_affected_rows($link) != 1) {
							trigger_error("De gebruikersgegevens zijn niet opgeslagen");
						} else {
							$getID = mysqli_insert_id($link);	// functie retourneert een integer
							$sendMailPath = ini_get('sendmail_path');

							// send e-mail
							if ($sendMailPath != NULL) {
								$to = $email;
								$from = "From: info@thisblog.nl";
								$subject = "Account aangemaakt bij ThisBlog";
								$body = "Welkom bij ThisBlog\r\n";
								$body .= "Uw accountgegevens zijn: \r\n";
								$body .= "Weergavenaam: " . $name . "\r\n";
								$body .= "E-mailadres: " . $email . "\r\n";
								$body .= "Wachtwoord: " . $password;
								if (mail($to, $subject,$body,$from)) {
									// Als naam gelijk is aan admin, zet als user ID 0
									// als user ID gelijk is aan 0, krijgt de gebruiker extra admin rechten in de andere scripts.
									if (strtolower($name) === "admin") {
										$_SESSION['userID'] = 0;
									} else {
										$_SESSION['userID'] = $getID;
									}
									header("Location: index.php");
								} else {
									$msg = "<p class=\"red\">Er ging iets mis met het versturen van de bevestigings-e-mail.</p>";
									$msg .= "<p class=\"red\">Neem aub contact op met de <a href=\"mailto:info@thisblog.nl\" class=\"red\">administrator.</a></p>";
								}
							} else {
								trigger_error('Er kunnen geen e-mails verstuurd worden');
							}
							unset($_POST);	// maak het formulier leeg
						}
					} else {
						$msg = "<p class=\"red\">Er is al een admin geregistreerd. Kies een andere weergavenaam</p>";
					}
				} else {
					$msg = "<p class=\"red\">Er is al een gebruiker met dit e-mailadres.</p>";
				}	
			}
		}

		?>
			<p>Vul je naam en e-mailadres in. Je ontvangt per e-mail een wachtwoord waarmee je kunt inloggen.</p>
			<p>Let op: De naam en domein van je e-mailadres moet uit minimaal 2 letters bestaan, mag geen cijfers of speciale tekens bevatten, bevat een @ en eindigt op .nl</p>
            <form action="registreren.php" method="POST" class="form">
				<table>
					<tr>
						<td>Naam</td>
						<td><input type="text" name="name" value="<?php if (isset($_POST['name'])) print $name; ?>"></td>
						<?php if (isset($invalidName)) print $invalidName; ?>
					</tr>
					<tr>
						<td>E-mail</td>
						<td><input type="text" name="email" value="<?php if (isset($_POST['email'])) print $email; ?>"></td>
						<?php if (isset($invalidEmail)) print $invalidEmail; ?>
                    </tr>
					<tr>
						<td></td>
						<td>
							<input type="submit" name="submit" class="button" value="Registreren">
						</td>
					</tr>
				</table>
			</form>
			<?php if(isset($msg)) print $msg; ?>
		</div>
	</div>
</body>
</html>