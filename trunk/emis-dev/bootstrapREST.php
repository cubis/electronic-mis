<?php
if (!isset($_SESSION['SESS_MEMBER_ID']))   // start session if it hasn't been
    session_start();

// connect to mysql server
$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$link) {
    die('Failed to connect to server: ' . mysql_error());
}

// select database
$db = mysql_select_db(DB_DATABASE);
if (!$db) {
    die("Unable to select database");
}

//Function to sanitize values received from the form. Prevents SQL injection
function clean($str) {
    $str = @trim($str);
    if (get_magic_quotes_gpc ()) {
        $str = stripslashes($str);
    }
    return mysql_real_escape_string($str);
}

function logToDB($actionDescription, $isLoggedIn)
{
	// Add this function to events of the program that should be logged.
	// Example: logToDB("User logged into the system", TRUE);
	
	// In the event that a user failed login, we will want to record the username by pulling
	// it from the form on the page and inserting it into $actionDescription.
	
	$ipAddress = @$REMOTE_ADDR;
	$type = $actionDescription;

	if($loggedIn) {
		// User is logged in. Add userID to FK_member_id in the table.
		$userID = $_SESSION['SESS_MEMBER_ID'];
		$dbLog = "INSERT INTO LogFiles (SourceIP, Type, PurgeDate, FK_member_id, TimeStamp) 
		VALUES ('$ipAddress', '$type', CURDATE() + INTERVAL '1' MONTH, '$userID', NOW())";
	}
	else {
		// User is not logged in. Do not add userID to FK_member_id in table.
		$dbLog = "INSERT INTO LogFiles (SourceIP, Type, PurgeDate, TimeStamp) 
		VALUES ('$ipAddress', '$type', CURDATE() + INTERVAL '1' MONTH, NOW())";
	}

	$result = mysql_query($dbLog);
}
?>