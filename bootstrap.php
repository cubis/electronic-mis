<?php
	if(!isset($_SESSION['SESS_MEMBER_ID']))			// start session if it hasn't been
		session_start();
		
	require_once('/electronic-mis/config.php');		// database connection details
	
	// connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link) {
		die('Failed to connect to server: ' . mysql_error());
	}
	
	// select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db) {
		die("Unable to select database");
	}
?>