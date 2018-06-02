<?php 
session_start(); 
setlocale(LC_TIME, 'nld_nld'); // nederlandse datumopmaak

// POST
$blogPostID = isset($_POST['blogPostID']) ? $_POST['blogPostID'] : NULL;	// id van de blogpost die bewerkt moet worden
$title = isset($_POST['title']) ? $_POST['title'] : NULL;
$content = isset($_POST['content']) ? $_POST['content'] : NULL;
// session
$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : NULL;			// ID-nummer van de ingelogde gebruiker
// GET
$bloggerID = isset($_GET['blogger']) ? (int) $_GET['blogger'] : NULL;		// ID-nummer van de schrijver van de weergegeven blog
$action = isset($_GET['action']) ? $_GET['action'] : NULL;					// toont blogposts of het formulier
// other vars
$privileges = $userID === 0 ? true : false;

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
				<h1>ThisBlog</h1>
			</div>
		</div>
		<?php 
		include 'includes/db-connectie.php';
		include 'includes/error-handler.php';
		include 'includes/nav-menu.php'; 
		?>
		<div class="blog-content">
		<?php

		/**
		 * De logica is opgesplitst in drie categorieen: Nieuw, Bewerken en Verwijderen.
		 * Bij Nieuw wordt een nieuw aangemaakte blogpost opgeslagen.
		 * Bij Bewerken wordt de relevante blogpost opgehaald.
		 * Klikt de gebruiker op Opslaan, dan wordt een er extra variabele gevuld, genaamd $_POST['saveEdits'].
		 * Het script haalt nogmaals de blogpost op, controleert of saveEdits is gevuld en controleert daarna of er iets is veranderd in de post.
		 * Zo ja, dan worden de aanpassingen opgeslagen. Zo nee, dan keert de gebruiker terug naar de blogweergave.
		 * Als derde kan de gebruiker een blogpost verwijderen.
		 * 
		 * Aan de hand van de waarde van $action wordt bepaald wat de gebruiker te zien krijgt. 
		 * Bij showForm wordt het formulier getoond en bij een lege waarde wordt de blog getoond.
		 * Heeft $action als waarde error dan wordt er niets getoond en is er sprake van een foutmelding.
		 */

		if (isset($_POST['submit']) && $_POST['submit'] === "Nieuw") {
			if (!$title || strlen($title) > 50) {
				$msg = "<p class=\"red\">Vul een titel in. Max 50 karakters.</p>";
			} elseif (!$content || strlen($_POST['content']) > 3000) {
				$msg = "<p class=\"red\">Vul de inhoud in van uw post. Max 3000 karakters.</p>";
			} else {
				if ($privileges) {
					// als de admin probeert een nieuwe post te maken, wordt een melding getoond.
					print "<p class=\"red\">Als admin mag je alleen posts bewerken.</p>";
					$action = "error";
				} else {
					$title = mysqli_real_escape_string($link, $title);
					$content = mysqli_real_escape_string($link, $content);
					$queryInsertPost = "INSERT INTO blogposts VALUES (NULL, $userID, NOW(), '$title', '$content')";
					$resultInsertPost = mysqli_query($link, $queryInsertPost);
					if (mysqli_affected_rows($link) != 1) {
						trigger_error("Blogpost is niet opgeslagen");
						$action = "error";						
					} else {
						header("Location: blog.php?blogger=$userID");
					}
				}
			}
		} elseif (isset($_POST['submit']) && $_POST['submit'] === "Bewerken") {
			$blogPostID = mysqli_real_escape_string($link, $blogPostID);
			/**
			 * Gebruikers mogen alleen hun eigen blogpost bewerken. De admin mag iedere blogpost bewerken. 
			 * Hiervoor zijn aparte queries gebruikt.
			 */
			if ($privileges)
				$queryGetPost = "SELECT * FROM blogposts WHERE ID = $blogPostID";
			else
				$queryGetPost = "SELECT * FROM blogposts WHERE ID = $blogPostID AND userID = $userID";
			
			$resultGetPost = mysqli_query($link, $queryGetPost);
			if (!$resultGetPost || mysqli_num_rows($resultGetPost) != 1) {
				trigger_error("Er is geen blogpost gevonden met het opgegeven ID-nummer");
				$action = "error";
			} else {
				$row = mysqli_fetch_array($resultGetPost);
				$fetchedBlogPostID = $row['ID'];
				$fetchedTitle = $row['title'];
				$fetchedContent = $row['content'];

				// pas als het formulier is ingevuld, is SaveEdits ook gevuld. 
				// De blogpost wordt nogmaals opgehaald, maar ditmaal om te vergelijken of er aanpassingen zijn gemaakt.
				if (isset($_POST['saveEdits']) && $_POST['saveEdits'] === "saveEdits") {
					if (!$title || strlen($title) > 50) {
						$msg = "<p class=\"red\">Vul een titel in. Max 50 karakters.</p>";
					} elseif (!$content || strlen($_POST['content']) > 3000) {
						$msg = "<p class=\"red\">Vul de inhoud in van uw post. Max 3000 karakters.</p>";
					} elseif ($fetchedTitle === $title && $fetchedContent === $content) {
						header("Location: blog.php?blogger=$userID");
					} else {
						$title = mysqli_real_escape_string($link, $title);
						$content = mysqli_real_escape_string($link, $content);
						
						if ($privileges)
							$queryUpdatePost = "UPDATE blogposts SET title = '$title', content = '$content' WHERE ID = '$blogPostID'";
						else
							$queryUpdatePost = "UPDATE blogposts SET title = '$title', content = '$content' WHERE ID = '$blogPostID' AND userID = '$userID'";
		
						$resultUpdatePost = mysqli_query($link, $queryUpdatePost);
						if (mysqli_affected_rows($link) != 1) {
							trigger_error("Blogpost is niet opgeslagen");
							$action = "error";
						} else {
							header("Location: blog.php?blogger=$userID");
						}
					} 
				}
			}
		} elseif (isset($_POST['submit']) && $_POST['submit'] === "Verwijderen") {
			if ($blogPostID) {
				$blogPostID = mysqli_real_escape_string($link, $blogPostID);

				if ($privileges)
					$queryDeletePost = "DELETE FROM blogposts WHERE ID = $blogPostID";
				else
					$queryDeletePost = "DELETE FROM blogposts WHERE ID = $blogPostID AND userID = $userID";

				$resultDeletePost = mysqli_query($link,$queryDeletePost);
				if (mysqli_affected_rows($link) != 1) {
					trigger_error("Blogpost is niet verwijderd");
				}
				header("Location: blog.php?blogger=$userID");
			}
		}

		/**
		 * Als action gelijk is aan $error hoeft er niets getoond te worden.
		 * Als $bloggerID niet is gevuld toon melding.
		 * Is $action gelijk aan showForm dan wordt in het formulier getoond, in alle andere gevallen wordt de blog getoond.
		 */

		 if ($action != "error") {
			if (is_null($bloggerID)) {
				print "<p class=\"red\">De inhoud van de pagina kan niet worden weergegeven.</p>";
			} elseif ($action === "showForm") {
			   $url = "blog.php?blogger=$bloggerID&action=showForm";
			   if (isset($fetchedTitle)) $fetchedTitle = str_replace("\"","&quot;",$fetchedTitle);
			   ?>
			   <p class="indent">Verzin een pakkende titel, typ je verhaal en klik daarna op Opslaan.</p>
			   <div class="form wide">
				   <form action="<?php print $url; ?>" method="post">
					   <table>
						   <tr>
							   <td>
								   <input type="hidden" name="blogPostID" value="<?php if (isset($fetchedBlogPostID)) print $fetchedBlogPostID; ?>">
								   <input type="hidden" name="saveEdits" value="saveEdits">
								   <label for="title">Titel</label>
								   <input type="text" name="title" id="title" value="<?php if (isset($fetchedTitle)) print $fetchedTitle; ?>">
							   </td>
						   </tr>
						   <tr>
							   <td>
								   <label for="content">Bericht</label>
								   <textarea name="content" id="content" cols="30" rows="20"><?php if (isset($fetchedContent)) print $fetchedContent; ?></textarea>
							   </td>
						   </tr>
						   <tr>
							   <td>
								   <?php
								   if ($blogPostID) {
									   print "<button type=\"submit\" name=\"submit\" class=\"button\" value=\"Bewerken\">Opslaan</button>";
								   } else {
									   print "<button type=\"submit\" name=\"submit\" class=\"button\" value=\"Nieuw\">Opslaan</button>";
								   }
								   ?>
								   <input type="reset" name="reset" class="button">
							   </td>
						   </tr>
					   </table>
				   </form>
			   </div>
			   <?php
			   if (isset($msg)) print $msg;
		   } else {

				/**
				 * Toon Blog
				 * Is de admin ingelogd en klikt hij op Bewerken in het menu dan is $bloggerID 0.
				 * Alle posts worden hierdoor opgehaald, in alle andere gevallen worden 
				 * alleen de posts van de betreffende gebruiker opgehaald.
				 */

			   if ($privileges && $bloggerID === 0) {
				   $queryGetAllPosts = 	"SELECT blogposts.ID, date, title, content, name 
										FROM blogposts 
										INNER JOIN users ON users.id = userID 
										ORDER BY date DESC";
			   } else {
				   $queryGetAllPosts = 	"SELECT blogposts.ID, date, title, content, name 
										FROM blogposts 
										INNER JOIN users ON users.id = userID 
										WHERE userID = $bloggerID 
										ORDER BY date DESC";
			   }
   
			   $resultGetAllPosts = mysqli_query($link, $queryGetAllPosts);
   
			   $url = "blog.php?blogger=$bloggerID&action=showForm";
   
			   if (mysqli_num_rows($resultGetAllPosts) > 0) {
				   while ($row = mysqli_fetch_array($resultGetAllPosts)) {

						// toon alleen eerste paragraaf
					   $contentArray = explode("\r\n", $row['content']);
					   $row['content'] = $contentArray[0];
					   // formatted string time
					   $time = strtotime($row['date']);
					   $time = strftime("%d %b %Y %H:%M", $time);
					   ?>
					   <div class="post">
						   <div class="title">
							   <h2><?php print $row['title']; ?></h2>
						   </div>
						   <div class="subtitle">
							   <span><?php print $row['name'] . " | " . $time; ?></span>
						   </div>
						   <div class="message">
							   <p><?php print $row['content']; ?></p>
							   <form action="<?php print $url; ?>" method="POST">
								   <input type="hidden" name="blogPostID" value="<?php print $row['ID']; ?>">
								   <a href="blog-lees-verder.php?blogpost=<?php print $row['ID']; ?>" class="inline-button">Lees verder</a>
								   <?php 
								   if ($userID === $bloggerID || $privileges) {
									   ?>
									   <input type="submit" name="submit" class="inline-button" value="Bewerken">
									   <input type="submit" name="submit" class="inline-button" value="Verwijderen">
									   <?php
								   }
								   ?>
							   </form>
						   </div>
					   </div>
					   <?php
				   }
			   } else {
				   print "<p>Deze blogger heeft nog geen blogposts gemaakt.</p>";
			   }
		   }
		} 
		?>
		</div>
	</div>
</body>
</html>