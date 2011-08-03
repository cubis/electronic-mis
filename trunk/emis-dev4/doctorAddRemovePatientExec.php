<?php
	require_once('auth.php');
	require_once('bootstrap.php');
	
$errmsg_arr = array();
$errflag = false;
$neg = -1;
global $currentPath;
$request = $currentPath . "doctorPatientREST.php?u=" . 
		urlencode($_SESSION['SESS_USERNAME']) . "&key=" . urlencode($_SESSION['SESS_AUTH_KEY']) . "&pat=" . 
		urlencode($_GET['ID']) . "&doc=";
		if($_GET['do'] == "remove"){
			$request .= urlencode($neg);
		} else if($_GET['do'] == "add"){
			$request .= urlencode($_SESSION['SESS_PERSONAL_ID']);
		}

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

$errNum = $wsResponse[$wsIndices['ERRNUM'][0]]['value'];

if($errNum == 0) {
	header("location: successPatientAddView.php");
	//print "OUTPUT = " . $output;
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
	header("location: failPatientAddView.php");
	exit();
}



?>