<?php

//Start session
//session_start();
//Array to store validation errors
$errmsg_arr = array();

//Validation error flag
$errflag = false;

//Function to sanitize values received from the form. Prevents SQL injection
function clean($str) {
    $str = @trim($str);
    if (get_magic_quotes_gpc()) {
        $str = stripslashes($str);
    }
    return mysql_real_escape_string($str);
}

//Sanitize the POST values
$type = 3;
//$appid = $_GET['ID'];
///////test to make sure get_id works -->might need to find a way to get to work
$id = $_GET[''];
$pat = $_POST[''];
$doc = $_POST[''];
$date = $_POST[''];
$time = $_POST[''];
$address = $_POST[''];
$status = $_POST[''];
$reason = $_POST[''];


$url = "http://localhost/emis/emis-dev3/visitREST.php";

$fields = array(
    'type' => urlencode($type),
    'id' => urlencode($id),
    'pat' => urlencode($pat),
    'date' => urlencode($date),
    'time' => urlencode($time),
    'address' => urlencode($address),
    'status' => urlencode($status),
    'reason' => urlencode($reason)
);

foreach ($fields as $key => $value) {
    $field_string .= $key . '=' . $value . '&';
}

rtrim($field_string, '&');



$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, count($fields));
curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
curl_setopt($ch, CURLOPT_TIMEOUT, 8);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);

curl_close($ch);

$parser = xml_parser_create();

xml_parse_into_struct($parser, $output, $wsResponse, $wsIndices);



$errNum = $wsResponse[$wsIndices['ERRNUM'][0]]['value'];

if ($errNum > 0) {
    $errflag = true;
    $ct = 0;
    while ($ct < $errNum) {
        $errmsg_arr[] = $wsResponse[$wsIndices['ERROR'][$ct]]['value'];
        $ct += 1;
    }
}

//If there are input validations, redirect back to the registration form
if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: visit.php");
    exit();
} else {
    header("location: visitSuccess.php");
    exit();
}
?>