<?php

require_once('config.php');     //sql connection information
require_once('bootstrap.php');  //link information

function outputXML($resultMsg) {
	$outputString = ''; //start empty
	$outputString .= '<?xml version="1.0"?>\n';
	$outputString .= "<result>".$resultMsg."</result>\n";
	return $outputString;
}

function doService($url, $method, $getArgs, $postArgs) {
	if($method == 'GET') {
        //qry database
        //
	//Create query
	$qry="SELECT * FROM Users WHERE UserName='$login' AND Password='".$getArgs['pw']."'";
	$result=mysql_query($qry);

		if($result)
			$retVal = outputXML('1');
		else
			$retVal = outputXML('0');
	} else
		$retVal = outputXML('0'); //fail if not GET method!!

	return $retVal;
}

// set up some useful variables
$serviceURL = $_SERVER['REQUEST_URI'];
$serviceMethod = strtoupper($_SERVER['REQUEST_METHOD']);
$getArgs = $_GET;
//$postArgs = $_POST; //don't care about post
$retVal = '';
$retVal = doService($serviceURL, $serviceMethod, $getArgs, $postArgs);
print($retVal);
return;
?>