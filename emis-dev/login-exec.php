<?php
	//Start session
	//session_start();
	
	//Include database connection details
	require_once('config.php');
	require_once('bootstrap.php');
	
	//Array to store validation errors
	$errmsg_arr = array();
	
	//Validation error flag
	$errflag = false;
	
	//Sanitize the POST values
	$login = clean($_POST['login']);
	$password = clean($_POST['password']);
	
	//Input Validations
	if($login == '') {
		$errmsg_arr[] = 'Login ID missing';
		$errflag = true;
	}
	if($password == '') {
		$errmsg_arr[] = 'Password missing';
		$errflag = true;
	}
	
	//If there are input validations, redirect back to the login form
	if($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header("location: index.php"); 
		exit();
	}
	/*
	//Create query
	$qry="SELECT * FROM Users WHERE UserName='$login' AND Password='".md5($_POST['password'])."'";
	$result=mysql_query($qry);
	*/
	//Check whether the query was successful or not
	
	$epw = md5($password); // encrypt password to lame ass md5 for t-fer

	//replace starting with your own webroot for debugging...
    
	$request = "http://localhost/emis/emis-dev/Authenticate.php?u=" . urlencode($login) . "&p=" . urlencode($epw);
//	print("URL: $request <br />\n");

	//format and send request
	$ch = curl_init($request);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);    
	curl_setopt($ch, CURLOPT_TIMEOUT, 8);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch); //send URL Request to RESTServer... returns string
	curl_close($ch); //string from server has been returned <XML> closethe channel
	
	if( $output == ''){
		die("CONNECTION ERROR OUTPUT=".$output);
	}	

	
	//print "OUTPUT: $output\n";
	
	//-----------------------------
	// TODO: Decode XML with parser and store the true/false in $result
	//Initialize the XML parser
	$parser = xml_parser_create();
	
	xml_parse_into_struct($parser, $output, $wsResponse, $wsIndices);
	
	
	//should be 3 elements inside <response> but parsed as: 
	//	0 response
	//	1 responsecode
	//	2 response
	//	3 responsemsg
	//	4 response
	//	5 responsedetail
	//	6 response
	//	7 response
	/*
	print("Response Code: Tag = " . $wsResponse[1]['tag'] . " Value = " . $wsResponse[1]['value'] . "<br />\n");
	print("Response Msg: Tag = " . $wsResponse[3]['tag'] . " Value = " . $wsResponse[3]['value'] . "<br />\n");
	print("Response Detail: Tag = " . $wsResponse[5]['tag'] . " Value = " . $wsResponse[5]['value'] . "<br />\n");
	*/
	
	$trustedKey = "xolJXj25jlk56LJkk5677LS";
	$result = $wsResponse[$wsIndices['RESULT'][0]]['value'];
	$key = md5($wsResponse[$wsIndices['KEY'][0]]['value'].$trustedKey);

	//print("OUTPUT = ".$output);
	if($result=='1') {
		//Login Successful
		session_regenerate_id();
		$qry="SELECT * FROM Users WHERE UserName='" .$login. "'";
		$qresult = mysql_query($qry);
		$member = mysql_fetch_assoc($qresult);
	
		/* $_SESSOION  will only hold variables for
		*
		* SESS_MEMBER_ID
		* SESS_MEMBER_TYPE
		* SESS_KEY  a hashed athentication key provided by auth server
		*
		*/
		$_SESSION['SESS_MEMBER_ID'] = $member['PK_member_id'];
		$_SESSION['SESS_FIRST_NAME'] = $member['FirstName'];			
		$_SESSION['SESS_LAST_NAME'] = $member['LastName'];
		$_SESSION['SESS_TYPE'] = $member['Type'];
		$_SESSION['SESS_USERNAME'] = $member['UserName'];
		$_SESSION['SESS_NEED_APPROVAL'] = $member['NeedApproval'];
		$_SESSION['SESS_AUTH_KEY'] = $key;
		session_write_close();
		//	die("ACCESS GAINED");
		header("location: member-profile.php");
		//print($output);
		exit();
		//Login failed
	
	}else {
		     	$errmsg_arr[] = 'Username or Password does not match';
			$errflag = true;
			header("location: index.php");
			exit();
	}

?>
	