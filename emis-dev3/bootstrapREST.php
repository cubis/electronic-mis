<?php
if (!isset($_SESSION['SESS_MEMBER_ID']))   // start session if it hasn't been
    session_start();

	
//new pdo connection stuff
try {
	$db = new PDO("mysql:dbname=".DB_DATABASE.";host=".DB_HOST,DB_USER,DB_PASSWORD);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}


function logToDB($actionDescription, $userID, $sentName)
{
	// Add this function to events of the program that should be logged.
	// Example: logToDB("User logged into the system", TRUE);
	// In the event that a user failed login, we will want to record the username by pulling
	// it from the form on the page and inserting it into $actionDescription.
	
	global $db;
	$ipAddress = $_SESSION["REMOTE_ADDR"];

	$type = $actionDescription;
	
	// User is logged in. Add userID to FK_member_id in the table.		
	$prep = $db->prepare("INSERT INTO LogFiles (SourceIP, Type, PurgeDate, FK_member_id, TimeStamp, UserName) 
					VALUES (:ipAddress, :type, CURDATE() + INTERVAL '1' MONTH, :userID, NOW(), :sentName)" );
	$insertLogSuccess = $prep->execute( array(':ipAddress'=>$ipAddress, ':type'=>$type, ':userID'=>$userID, ':sentName'=>$sentName) );
	return $insertLogSuccess;
	
	return '';
	
}
?>