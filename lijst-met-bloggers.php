<?php 
session_start(); 
?>
<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<title>Bloggers | ThisBlog</title>
	<link rel="stylesheet" href="css/thema.css">
</head>
<body>
	<div class="container">
		<div id="banner">
			<div id="banner-content">
				<h1>Bloggers</h1>
			</div>
		</div>
        <?php 
		include 'includes/db-connectie.php';
		include 'includes/error-handler.php';
		include 'includes/nav-menu.php'; 
		?>
		<div class="blog-content">
            <h2>Bloggers</h2>
            <?php
			print "<ul>";
			if (count($users) > 0) {
				foreach($users as $user) {
					print "<li><a href=\"blog.php?blogger=$user[ID]\">$user[name]</a></li>\n";
				}
				print "</ul>";
			} else {
				print "</ul>";
                print "<p>Er zijn nog geen bloggers. Schrijf je in en wordt de eerste blogger.</p>\n";
			}
            ?>
		</div>
	</div>
</body>
</html>