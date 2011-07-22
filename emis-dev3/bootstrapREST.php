<?php
if (!isset($_SESSION['SESS_MEMBER_ID']))   // start session if it hasn't been
    session_start();

	
//new pdo connection stuff
try {
	$db = new PDO("mysql:dbname=".DB_DATABASE.";host=".DB_HOST,DB_USER,DB_PASSWORD);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}


function logToDB($actionDescription, $isLoggedIn, $userID, $db)
{
	// Add this function to events of the program that should be logged.
	// Example: logToDB("User logged into the system", TRUE);
	
	// In the event that a user failed login, we will want to record the username by pulling
	// it from the form on the page and inserting it into $actionDescription.
	
	
	$ipAddress = $_SESSION["REMOTE_ADDR"];

	$type = $actionDescription;
	
	if($isLoggedIn) {
		// User is logged in. Add userID to FK_member_id in the table.		
		$prep = $db->exec("INSERT INTO LogFiles (SourceIP, Type, PurgeDate, FK_member_id, TimeStamp) VALUES ('$ipAddress', '$type', CURDATE() + INTERVAL '1' MONTH, '$userID', NOW())" );		
	} else {
		// User is not logged in. Do not add userID to FK_member_id in table.		
		$prep = $db->exec("INSERT INTO LogFiles (SourceIP, Type, PurgeDate, TimeStamp) VALUES ('$ipAddress', '$type', CURDATE() + INTERVAL '1' MONTH, NOW())");	
	}
	return '';
	
}
?>