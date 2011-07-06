<?php
function output($resultCode, $resultMsg, $resultDetail) {
	$outputString = '';
	$outputString .= '<?xml version="1.0"?>';
	$outputString .= "\n";

	$outputString .= "<response>\n";
	$outputString .= "<responsecode>" . $resultCode . "</responsecode>\n";
	$outputString .= "<responsemsg>" . $resultMsg . "</responsemsg>\n";
	$outputString .= "<responsedetail>" . $resultDetail . "</responsedetail>\n";
	$outputString .="<responsecode> STUFF </responsecode>\n";

	$outputString .= "</response>\n";
	return $outputString;
}

function doService($url, $method, $getArgs, $postArgs) {
	$retVal = "METHOD = " . $method;
	if($method == 'GET') {
		if(strtoupper($getArgs['login']) == 'TEST' && strtoupper($getArgs['pw']) == 'TEST')
			$retVal = output('OK', '1', 'GET AUTHENTICATION SUCCESS');
		else
			$retVal = output('OK', '0', 'GET AUTHENTICATION FAILURE');
	} else if($method == 'POST') {
		if(strtoupper($postArgs['login']) == 'TEST' && strtoupper($postArgs['pw']) == 'TEST')
			$retVal = output('OK', '1', 'POST AUTHENTICATION SUCCESS');
		else
			$retVal = output('OK', '0', 'POST AUTHENTICATION SUCCESS');
	} else 
		return 'UNKNOWN METHOD';
	return $retVal;
}

// set up some useful variables
$serviceURL = $_SERVER['REQUEST_URI'];
$serviceMethod = strtoupper($_SERVER['REQUEST_METHOD']);
$getArgs = $_GET;
$postArgs = $_POST;

$retVal = doService($serviceURL, $serviceMethod, $getArgs, $postArgs);
print("<HTML><H2>$retVal</H2></HTML>");
return;
?>
