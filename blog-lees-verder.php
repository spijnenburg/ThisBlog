<?php 
session_start(); 
setlocale(LC_TIME, 'nld_nld'); // nederlandse datumopmaak
$blogPostID = isset($_GET['blogpost']) ? (int) $_GET['blogpost'] : NULL;
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

		// Prepared statement practice

		if ($blogPostID) {
			$queryBlogPost = 	"SELECT blogposts.ID, date, title, content, name 
								FROM blogposts 
								INNER JOIN users ON users.id = userID 
								WHERE blogposts.ID = ?";

			$statement = mysqli_prepare($link, $queryBlogPost);
			mysqli_stmt_bind_param($statement, "i", $blogPostID);
			mysqli_stmt_execute($statement);
			$resultBlogPost = mysqli_stmt_get_result($statement);

			if (mysqli_num_rows($resultBlogPost) === 1) {
				$row = mysqli_fetch_array($resultBlogPost);
				$row['content'] = str_replace("\r\n","<br>",$row['content']);
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
					</div>
				</div>
				<?php
			} else {
				print "<p class=\"red\">Er is geen blogpost gevonden met het opgegeven ID-nummer.";
			}
		}
			?>
		</div>
	</div>
</body>
</html>