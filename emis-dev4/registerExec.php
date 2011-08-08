<?php

	//Start session
	session_start();
	require_once("auth.php");
	require_once("bootstrap.php");


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
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$login = $_POST['login'];
	$bday = $_POST['bday'];
	$email = $_POST['email'];
	$ssn = $_POST['ssn'];
	$type = $_POST['type'];
	$password = $_POST['password'];
	$cpassword = $_POST['cpassword'];

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
	global $currentPath;
	
	$url = $currentPath . "registerREST.php";

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
  
	$field_string = rtrim($field_string, '&');
	
	
	    
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, count($fields) );
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
	
	if($errNum > 0){
		$errflag = true;
		$ct = 0;
		while($ct < $errNum){
			$errmsg_arr[] = $wsResponse[$wsIndices['ERROR'][$ct]]['value'];
			$ct += 1;
		}
	}
	
	//If there are input validations, redirect back to the registration form
	if ($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header("location: registerView.php");
		exit();
	} else {
		header("location: registerSuccessView.php");
		exit();
	}

	
?>
