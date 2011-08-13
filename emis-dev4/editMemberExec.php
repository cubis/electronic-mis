<?php
require_once("auth.php");
require_once('bootstrap.php');	// Include database connection details

$errmsg_arr = array();
$errflag = false;

$username = $_POST['UserName'];
$AuthKey = $_POST['AuthKey'];
$CallingUserName = $_POST['CallingUserName'];

$fieldString = '';
foreach ($_POST as $key => $value) {
	$fieldString .= $key . "=";
	$fieldString .= urlencode($value) . "&";
}
/*
$fieldString = rtrim($fieldString, "&");
print "$fieldString\n";

$text = print_r($_POST, true);
print "<pre>$text</pre>";
*/
global $currentPath;
$request = $currentPath . "editMemberREST.php";
//format and send request
$ch = curl_init($request);
curl_setopt($ch, CURLOPT_POST, count($_POST) );
curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldString);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);    
curl_setopt($ch, CURLOPT_TIMEOUT, 8);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$RESToutput = curl_exec($ch); //send URL Request to RESTServer... returns string
curl_close($ch); //string from server has been returned <XML> closethe channel

if( $RESToutput == ''){
	die("CONNECTION ERROR");
}	

//die($RESToutput);
//print $RESToutput;
//parse return string
$parser = xml_parser_create();	
xml_parse_into_struct($parser, $RESToutput, $wsResponse, $wsIndices);

$errNum = $wsResponse[$wsIndices['ERRNUM'][0]]['value'];

print $RESToutput;

/*
if($errNum != 0) {
	//logout failed...output error to screen
	$ct = 0;
	while($ct < $errNum){
		$errmsg_arr[] = $wsResponse[$wsIndices['ERROR'][$ct]]['value'];
		$ct += 1;
		//print $wsResponse[$wsIndices['ERROR'][$ct]]['value'] . "<br />";
	}
	$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
	session_write_close();
	header("location: index.php");
	exit();
}
*/

?>
<!--
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="refresh" content="1;url=http://localhost/emis/emis-dev4/memberProfileView.php">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="css/styles.css" rel="stylesheet" type="text/css" />
	<title>Success</title>
</head>

<body>
<center>
<div class="login_box">
<img src="img/logo.png" alt="Electronic Medical Information System">
<h1> Edit Successful. You will be re-directed in 2 seconds. </h1>
</div>
</body>
</html>
-->
