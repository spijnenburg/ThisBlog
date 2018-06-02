<?php

/**
 * Als de query mislukt wordt er een error getoond.
 * Vervolgens wordt gecontroleerd of het resultaat niet false retourneert.
 * Is dit wel het geval dan wordt het script gestopt.
 */
$queryGetUsers = "SELECT ID, name FROM users";
$resultGetUsers = mysqli_query($link, $queryGetUsers) or trigger_error("Het navigatie menu kan niet geladen worden");

if ($resultGetUsers) {
	$users = array();
	$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : NULL;
	$privileges = $userID === 0 ? true : false;
	$activeUser;	// naam van de ingelogde gebruiker

	/**
	 * Query resultaat wordt opgehaald. 
	 * Als het ID overeenkomt met het userID van de ingelogde gebruiker dan wordt dit in de variabele $activeUser geplaatst,
	 * vervolgens wordt het resultaat in een array geplaast genaamd $users.
	 */

	while ($row = mysqli_fetch_assoc($resultGetUsers)) {

		// De naam van gebruiker wordt in de variabele $activeUser gezet, voor de admin geldt een uitzondering.

		if ($userID === 0) {
			$activeUser = "Admin";
		} else if ($userID === (int) $row['ID']) {	// $row['ID'] retourneert een string, daarom wordt gecast
			$activeUser = $row['name'];
		}
		// als de gebruikersnaam niet gelijk is aan admin, voeg gebruiker toe aan array
		if (strtolower($row['name']) != "admin") array_push($users, $row);	
	}
	?>
	<div class="sidebar">
		<ul>
			<?php
			if ($userID === 0 || $userID > 0) {
				?>
				<p class="smallcursive">Je bent ingelogd als:<br><?php print $activeUser; ?> 
				<li><a href="index.php">Home</a></li>
				<li><a href="uitloggen.php">Uitloggen</a></li>
				<?php
				if (!$privileges) {
					?>
					<li><a href="blog.php?blogger=<?php print $userID; ?>&action=showForm">Nieuwe post</a></li>
					<?php
				}
				?>
				<li><a href="blog.php?blogger=<?php print $userID; ?>">Bewerken</a></li>
				
				<?php
			} else {
				?>
				<li><a href="index.php">Home</a></li>
				<li><a href="inloggen.php">Inloggen</a></li>
				<li><a href="registreren.php">Registreren</a></li>
				<?php
			}
			?>
		</ul>
		<ul>
			<li><a href="lijst-met-bloggers.php">Bloggers</a></li>
			<ul>
				<?php
				if (count($users) > 0) {
					foreach ($users as $user) {
						print "<li><a href=\"blog.php?blogger=$user[ID]\">$user[name]</a></li>\n";					
					}
				} else 
					print "<li class=\"smallcursive\">Schrijf je in en wordt de eerste blogger!</li>\n";
				?>
			</ul>
		</ul>
	</div>
	<?php
} else {
	// als het navigatiemenu niet kan laden, mag het script stoppen.
	exit();
}
?>

