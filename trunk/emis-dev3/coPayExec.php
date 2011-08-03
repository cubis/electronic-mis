<?php
session_start();
require_once('auth.php');
require_once('configREST.php');
require_once('bootstrapREST.php');


$errmsg_arr = array();

$url = "http://localhost/emis/emis-dev3/coPayREST.php";
$amount = $_POST['amount'];
$month = $_POST['month'];
$day = $_POST['day'];
$year = $_POST['year'];
$appID = $_GET['ID'];

$fields = array(
    'u' => urlencode($_SESSION['SESS_USERNAME']),
    'key' => urlencode($_SESSION['SESS_AUTH_KEY']),
    'amount' => urlencode($amount),
    'month' => urlencode($month),
    'day' => urlencode($day),
    'year' => urlencode($year),
    'appID' => urlencode($appID)
);

foreach ($fields as $key => $value) {
    $field_string .= $key . '=' . $value . '&';
}
$field_string = rtrim($field_string, '&');

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 0);
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
    header("location: appointment.php");
    exit();
} else {
    header("location: memberProfileView.php");
    exit();
}
?>
