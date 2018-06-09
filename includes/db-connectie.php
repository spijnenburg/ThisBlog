<?php

$host = "localhost";
$user = "myname";
$password = "mypassword";
$database = "ThisBlog";

@$link = mysqli_connect($host, $user, $password);

if ($link) {
    if (!mysqli_select_db($link, $database)) {
        trigger_error("Kan niet verbinden met database");
        exit;
    }
} else {
    trigger_error("Kan niet met server verbinden");
    exit;
}

?>  