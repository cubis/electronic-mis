<?php

	//Start session
	session_start();

	//Include database connection details
	require_once('config.php');

	//Array to store validation errors
	$errmsg_arr = array();

	//Validation error flag
	$errflag = false;

	//Function to sanitize values received from the form. Prevents SQL injection
	function clean($str) {
		$str = @trim($str);
		if (get_magic_quotes_gpc ()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);
	}

	//Sanitize the POST values
	$fname = clean($_POST['fname']);
	$lname = clean($_POST['lname']);
	$login = clean($_POST['login']);
	$bday = clean($_POST['bday']);
	$email = clean($_POST['email']);
	$ssn = clean($_POST['ssn']);

	$password = clean($_POST['password']);
	$cpassword = clean($_POST['cpassword']);

	//Input Validations
/*	if ($fname == '') {
		$errmsg_arr[] = 'First name missing';
		$errflag = true;
	}
	if ($lname == '') {
		$errmsg_arr[] = 'Last name missing';
		$errflag = true;
	}
	//test
	if ($bday == '') {
		$errmsg_arr[] = 'birthdate name missing';
		$errflag = true;
	}
	if ($email == '') {
		$errmsg_arr[] = 'e-mail address missing';
		$errflag = true;
	}
	if ($ssn == '') {
		$errmsg_arr[] = 'Social Security Number missing';
		$errflag = true;
	}
	//end
	if ($login == '') {
		$errmsg_arr[] = 'Login ID missing';
		$errflag = true;
	}
	if ($password == '') {
		$errmsg_arr[] = 'Password missing';
		$errflag = true;
	}
	if ($cpassword == '') {
		$errmsg_arr[] = 'Confirm password missing';
		$errflag = true;
	}
	if (strcmp($password, $cpassword) != 0) {
		$errmsg_arr[] = 'Passwords do not match';
		$errflag = true;
	}

	if (!ctype_alnum($password)) {
		$errmsg_arr[] = 'Password should be numbers & Digits only';
		$errflag = true;
	}

	if (strlen($password) < 7) {
		$errmsg_arr[] = 'Password must be at least 7 chars';
		$errflag = true;
	}

	if (strlen($password) > 20) {
		$errmsg_arr[] = 'Password must be at most 20 chars ';
		$errflag = true;
	}
	if (!preg_match('`[A-Z]`', $password)) {
		$errmsg_arr[] = 'Password must contain at least one upper case';
		$errflag = true;
	}

	if (!preg_match('`[a-z]`', $password)) {
		$errmsg_arr[] = 'Password must contain at least one lower case';
		$errflag = true;
	}

	if (!preg_match('`[0-9]`', $password)) {
		$errmsg_arr[] = 'Password must contain at least one digit';
		$errflag = true;
	}
	*/
	//Check for duplicate login ID

	$url = "http://localhost/emis/emis-dev3/registerREST.php";

	$fields = array(
		'fname' => urlencode($fname), 
		'lname' => urlencode($lname),
		'bday' => urlencode($bday), 
		'email' => urlencode($email),
		'ssn' => urlencode($ssn), 
		'u' => urlencode($login), 
		'p' => urlencode($password),
		'cp' => urlencode($cpassword),
		'type' => urlencode($type)
	);

	foreach($fields as $key=>$value){
		$field_string .= $key.'='.$value.'&';
	}
  
	rtrim($field_string, '&');
    
    
    
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
	
	

	print("OUTPUT = ".$output);



/*if ($login != '') {
    $qry = "SELECT * FROM Users WHERE UserName='$login'";
    $result = mysql_query($qry);
    if ($result) {
        if (mysql_num_rows($result) > 0) {
            $errmsg_arr[] = 'Login ID already in use';
            $errflag = true;
        }
        @mysql_free_result($result);
    } else {
        die("Query failed");
    }
}

//If there are input validations, redirect back to the registration form
if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: registerForm.php");
    exit();
}

if (strcmp($_POST['type'], "patient") == 0)
    $qry = "INSERT INTO Users(FirstName, LastName, UserName, Email, Birthday, SSN, Type, NeedApproval, Password) VALUES('$fname','$lname','$login','$email','$bday','$ssn','1','0','" . md5($_POST['password']) . "')";

elseif (strcmp($_POST['type'], "nurse") == 0)
    $qry = "INSERT INTO Users(FirstName, LastName, UserName, Email, Birthday, SSN, Type, NeedApproval, Password) VALUES('$fname','$lname','$login','$email','$bday','$ssn','200','1','" . md5($_POST['password']) . "')";

elseif (strcmp($_POST['type'], "doctor") == 0)
    $qry = "INSERT INTO Users(FirstName, LastName, UserName, Email, Birthday, SSN, Type, NeedApproval, Password) VALUES('$fname','$lname','$login','$email','$bday','$ssn','300','1','" . md5($_POST['password']) . "')";

if (strcmp($_POST['type'], "admin") == 0)
    $qry = "INSERT INTO Users(FirstName, LastName, UserName, Email, Birthday, SSN, Type, NeedApproval, Password) VALUES('$fname','$lname','$login','$email','$bday','$ssn','400','1','" . md5($_POST['password']) . "')";

$result = @mysql_query($qry);
*/


	//Check whether the query was successful or not
	if ($result) {
		header("location: registerSuccess.php");
		exit();
	} else {
		die("Query failed");
	}
?>
