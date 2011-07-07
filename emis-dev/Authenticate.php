<?php
/* Assumptions:  transfer is over secure https
 *               password is already hashed+salt before transfer
 *
 * Access:  This WS may be accessed by anyone
 * Input:  https://[URL]/Authenticate.php?u=[username]&password=[pass]
 * Output: XML
 *   result  [0 or 1]  if user and pass is correct
 *   key     [hashed key] validate auth was performed on this WS
 *
 *
 ****EXAMPLE OUTPUT DIGEST*****
 *  <?xml version="1.0"?>
 *      <result>    1       </result>
 *      <key>       fb504a91465213203ae7c3866bbf3cf4</key>
 *      <userID>    12345   </userID>
 *      <AccessType>400     </type>
 *
 *  */




require_once('config.php');     //sql connection information
require_once('bootstrap.php');  //link information

function outputXML($resultMsg) {
/* @var $AUTH_KEY A key that will be used to prove authentication occurred from this service. */
	$AUTH_KEY = md5("3p1XyTiBj01EM0360lFw");
	
	$outputString = ''; //start empty
	$outputString .= "<?xml version=\"1.0\"?>\n";
	$outputString .= "<result>" . $resultMsg . "</result>\n";
    $outputString .= "<key>" . $AUTH_KEY . "</key>\n";

	return $outputString;
}

function doService() {
	$qry="SELECT * FROM Users WHERE UserName='" . $_GET['u'] . "' AND Password='" . $_GET['p'] . "'";
	$result=mysql_query($qry);

	if($result)
		$retVal = outputXML('1');
	else
		$retVal = outputXML('0');
		
	return $retVal;
}

// set up some useful variables
//$serviceURL = $_SERVER['REQUEST_URI'];
//$serviceMethod = strtoupper($_SERVER['REQUEST_METHOD']);
//$getArgs = $_GET;
//$postArgs = $_POST; //don't care about post
//$retVal = doService($serviceURL, $serviceMethod, $getArgs);

$output = doService();
print($output);

?>