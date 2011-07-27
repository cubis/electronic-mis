<?php
session_start();
require_once('auth.php');
require_once('configREST.php');
require_once('bootstrapREST.php');


$errmsg_arr = array();

/* $url = "http://localhost/emis/emis-dev3/visitREST.php";

  $fields = array(
  'u' => urlencode($_SESSION['SESS_USERNAME']),
  'key' => urlencode($_SESSION['SESS_AUTH_KEY']),
  'pass' => urlencode($_POST['oldpass']),
  'newpass1' => urlencode($_POST['newpass1']),
  'newpass2' => urlencode($_POST['newpass2'])
  );

  foreach($fields as $key=>$value){
  $field_string .= $key.'='.$value.'&';
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


  //	print("OUTPUT = " . $output);


  $errNum = $wsResponse[$wsIndices['ERRNUM'][0]]['value'];

  if($errNum == 0){
  $errmsg_arr[] = "SUCCESSFUL PASSWORD CHANGE";

  } else {
  $ct = 0;
  while($ct < $errNum){
  $errmsg_arr[] = $wsResponse[$wsIndices['ERROR'][$ct]]['value'];
  $ct++;
  }
  }
  $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
  session_write_close(); */
/* header("location: appointment.php"); */


$url = "http://localhost/emis/emis-dev3/addApptREST.php";
$doctor = $_POST['doctor'];
$month = $_POST['month'];
$day = $_POST['day'];
$year = $_POST['year'];
$hour = $_POST['hour'];
$reason = $_POST['reason'];
$reminder = $_POST['reminder'];

echo 'Doctor: ' . $doctor . '<br />';
echo 'Month: ' . $month . '<br />';
echo 'Day: ' . $day . '<br />';
echo 'Year: ' . $year . '<br />';
echo 'Hour: ' . $hour . '<br />';
echo 'Reason: ' . $reason . '<br />';
echo 'Reminder: ' . $reminder . '<br />';

$fields = array(
    'u' => urlencode($_SESSION['SESS_USERNAME']),
    'key' => urlencode($_SESSION['SESS_AUTH_KEY']),
    'doctor' => urlencode($doctor),
    'month' => urlencode($month),
    'day' => urlencode($day),
    'year' => urlencode($year),
    'hour' => urlencode($hour),
    'reason' => urlencode($reason),
    'reminder' => urlencode($reminder)
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

// if ($errNum > 0) {
//     $errflag = true;
//     $ct = 0;
//     while ($ct < $errNum) {
//         $errmsg_arr[] = $wsResponse[$wsIndices['ERROR'][$ct]]['value'];
//         $ct += 1;
//     }
// }

// //If there are input validations, redirect back to the registration form
// if ($errflag) {
//     $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
//     session_write_close();
//     header("location: visit.php");
//     exit();
// } else {
    header("location: memberProfileView.php");
//     exit();
// }
?>
