<?php
	//Start session
	session_start();
	
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

    $request = "AuthenticateREST.php?login=" . urlencode($login) . "&pw=" . urlencode($epw);
    print("URL: $request <br />\n");

    //format and send request
    $ch = curl_init($request);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch); //send URL Request to RESTServer... returns string
    curl_close($ch); //string from server has been returned <XML> closethe channel

    //-----------------------------
    // TODO: Decode XML with parser and store the true/false in $result


	if($result=='1') {
		if(mysql_num_rows($result) == 1) {
			//Login Successful
			session_regenerate_id();
			$member = mysql_fetch_assoc($result);
			$_SESSION['SESS_MEMBER_ID'] = $member['PK_member_id'];
			$_SESSION['SESS_FIRST_NAME'] = $member['FirstName'];
			$_SESSION['SESS_LAST_NAME'] = $member['LastName'];
			$_SESSION['SESS_TYPE'] = $member['Type'];
			$_SESSION['SESS_USERNAME'] = $member['UserName'];
			$_SESSION['SESS_NEED_APPROVAL'] = $member['NeedApproval'];
			session_write_close();
			header("location: member-profile.php");
			exit();
		}else {
			//Login failed
                    	$errmsg_arr[] = 'Username or Password does not match';
                        $errflag = true;
			header("location: index.php");
			exit();
		}
	}else {
            die("Query failed");
	}
?>
