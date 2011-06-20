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

        if($fname == '') {
                $errmsg_arr[] = 'First name missing';
                $errflag = true;
        }
        if($lname == '') {
                $errmsg_arr[] = 'Last name missing';
                $errflag = true;
        }
        if($login == '') {
                $errmsg_arr[] = 'Login ID missing';
                $errflag = true;
        }
        if($pass == '') {
                $errmsg_arr[] = 'Password missing';
                $errflag = true;
        }
        if($pass2 == '') {
                $errmsg_arr[] = 'Password Confirm missing';
                $errflag = true;
        }
        if(strcmp($password, $cpassword) != 0 ){
            $errmsg_arr[] = 'Passwords do not match';
            $errflag = true;
        }
        if(!ctype_alnum($pass)){
                $errmsg_arr[] = 'Password should be numbers & Digits only';
                $errflag = true;
        }
        if(strlen($pass)<7) {
                $errmsg_arr[] = 'Password must be at least 7 chars';
                $errflag = true;
        }
        if(strlen($pass)>20){ 
                $errmsg_arr[] = 'Password must be at most 20 chars ';
                $errflag = true;
        }
        if(! preg_match('`[A-Z]`',$pass)){ 
                $errmsg_arr[] = 'Password must contain at least one upper case';
                $errflag = true;
        }
        if(! preg_match('`[a-z]`',$pass)){  
                $errmsg_arr[] = 'Password must contain at least one lower case';
                $errflag = true;
        }
       
        if(!preg_match('`[0-9]`',$pass)) {
                $errmsg_arr[] = 'Password must contain at least one digit';
                $errflag = true;
        }   
        if(!preg_match('`^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$`', $email)){
                $errmsg_arr[] = 'Format for Email not correct (foo@bar.com)';
                $errflag = true;
        }
        if(!preg_match('`^([1-3][0-9]{3,3})-(0?[1-9]|1[0-2])-(0?[1-9]|[1-2][1-9]|3[0-1])$`',$bday)){
            $errmsg_arr[] = 'Format for Bday not right (YYYY-MM-DD)';
            $errflag = true;
        }
  if(!preg_match(`'^[0-9]{10}$`',$phone)){
            $errmsg_arr[] = 'Wrong format for phon (##########)';
            $errflag = true;
        }
        if(!preg_match('^[0-9]{9}$', $ssn)){
            $errmsg_arr[] = 'SSN Missing or wrong format (#########)';
            $errflag = true;
        }
//Check for duplicate login ID
        if($uname!= '') {
                $qry = "SELECT * FROM Users WHERE UserName='$uname'";
                $result = mysql_query($qry);
                if($result) {
                        if(mysql_num_rows($result) > 0) {
                                $errmsg_arr[] = 'Login ID already in use';
                                $errflag = true;
                        }
                        @mysql_free_result($result);
                }
                else {
                        die("Query failed");
                }
        }
        
        //If there are input validations, redirect back to the registration form
        if($errflag) {
                $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
                session_write_close();
                header("location: create_user.php");
                exit();
        }
$query1 = "INSERT INTO Users ('". $fname. "','". $lname ."','". $pass ."','". $sex ."','". $uname. "','". $type ."','". $email ."','". $bday ."','". $phone ."','". $ssn. "','". $expdate ."','". $locked. "');";
  //$con = mysql_connect($host, $uname, $pass);
//@mysql_select_db($db) or die('unable to select DB');

if(!mysql_query($query1)){
	die('Error: '. mysql_error());
}

  /*
if(!mysql_query($query2)){
	die('Error: '. mysql_error());
}
  */
  //mysql_close($link);
?>

<html>
<body>
<h1>Request Sent, Go back to site home</h1>
<a href="index.php">Home</a>
</body>
</html>