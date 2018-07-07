
	<?php
	$host = 'localhost';
	$user = 'stefan';
	$password = 'mypassword';
	$database = 'thisblog';
	
	@$link = mysqli_connect($host, $user, $password);
	
	if ($link) {
		if (!mysqli_select_db($link, $database)) {
			trigger_error('Kan niet verbinden met database');
			exit;
		}
	} else {
		trigger_error('Kan niet met server verbinden');
		exit;
	}
	?> 