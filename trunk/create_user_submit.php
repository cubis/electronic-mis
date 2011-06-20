<?php
    require_once('auth.php');       
    require_once('config.php');
    require_once('bootstrap.php');


  /*
$host = 'localhost';
$uname = '495311team2user';
$pass = '680c12D5!gP592xViF';
$db = 'cs49532011team2';
  */
$fname = $_POST['firstname'];
$lname = $_POST['lastname'];
$pass = $_POST['pass'];
$sex = $_POST['sex'];
$uname = $_POST['uname'];
$type = $_POST['accttype'];
$email = $_POST['email'];
$bday = $_POST['Birthday'];
$phone = $_POST['pnumber'];
$ssn = $_POST['SSN'];
$expdate = '';
$locked = 0;
$pass2 = $_POST['pass2'];

if ($type == 1){
	$query2 = "INSERT INTO Patient ('".$ssn."');";
}

$query1 = "INSERT INTO Users ('". $fname. "','". $lname ."','". $pass ."','". $sex ."','". $uname. "','". $type ."','". $email ."','". $bday ."','". $phone ."','". $ssn. "','". $expdate ."','". $locked. "');";
$con = mysql_connect($host, $uname, $pass);
//@mysql_select_db($db) or die('unable to select DB');

if(!mysql_query($query1)){
	die('Error: '. mysql_error());
}
if(!mysql_query($query2)){
	die('Error: '. mysql_error());
}

  //mysql_close($link);
?>

<html>
<body>
<h1>Request Sent, Go back to site home</h1>
<a href="index.html">Home</a>
</body>
</html>