<?php
	include 'config.php';
	
	session_start();
	unset($_SESSION['user_info']);
	empty($_SESSION);
	session_destroy();

	//echo "asdasd";
	header("Location: ".SITE_URL);

?>