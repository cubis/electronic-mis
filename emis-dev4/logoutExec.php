<?php
require_once('bootstrap.php');	// Include database connection details

$errmsg_arr = array();
$errflag = false;
global $currentPath;
$request = $currentPath . "logoutREST.php?u=" . 
		urlencode($_SESSION['SESS_USERNAME']) . "&k=" . urlencode($_SESSION['SESS_AUTH_KEY']);

//format and send request
$ch = curl_init($request);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);    
curl_setopt($ch, CURLOPT_TIMEOUT, 8);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch); //send URL Request to RESTServer... returns string
curl_close($ch); //string from server has been returned <XML> closethe channel

if( $output == ''){
	die("CONNECTION ERROR");
}	

//parse return string
$parser = xml_parser_create();	
xml_parse_into_struct($parser, $output, $wsResponse, $wsIndices);

$result = $wsResponse[$wsIndices['RESULT'][0]]['value'];
$errNum = $wsResponse[$wsIndices['ERRNUM'][0]]['value'];

if($result == '1' && $errNum == 0) {
	// logout successful
	session_unset();
    session_destroy();
	header("location: index.php");
	exit();
}
else {
	//logout failed...output error to screen
	$ct = 0;
	while($ct < $errNum){
		$errmsg_arr[] = $wsResponse[$wsIndices['ERROR'][$ct]]['value'];
		$ct += 1;
	}		
	$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
	session_write_close();
	header("location: index.php");
	exit();
}

?>
	