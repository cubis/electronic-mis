<?php
	
	//Include database connection details
	require_once('bootstrap.php');
	
	//Array to store validation errors
	$errmsg_arr = array();
	
	//Validation error flag
	$errflag = false;
	
	//Sanitize the POST values
	$login = $_POST['login'];
	$password = $_POST['password'];	
	$epw = md5($password); // encrypt password to lame ass md5 for t-fer

	//replace starting with your own webroot for debugging...
    
	$request = "http://localhost/emis/emis-dev3/authenticateREST.php?u=" . urlencode($login) . "&p=" . urlencode($epw);

	//format and send request
	$ch = curl_init($request);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);    
	curl_setopt($ch, CURLOPT_TIMEOUT, 8);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch); //send URL Request to RESTServer... returns string
	curl_close($ch); //string from server has been returned <XML> closethe channel
	
	if( $output == ''){
		die("CONNECTION ERROR");
	}	

	//parse return string
	$parser = xml_parser_create();	
	xml_parse_into_struct($parser, $output, $wsResponse, $wsIndices);
	
	//print( "OUTPUT|$output|\n");
	//print "<pre>";
	//print_r($wsResponse);
	//print_r($wsIndices);
	//print "</pre>";
		
	//create trusted key from the given auth key and trusted string
	$trustedKey = "xolJXj25jlk56LJkk5677LS";
	$key = md5($wsResponse[$wsIndices['KEY'][0]]['value'].$trustedKey);
	
	//print("KEY: " . $wsResponse[$wsIndices['KEY'][0]]['value']);
	$errNum = $wsResponse[$wsIndices['ERRNUM'][0]]['value'];
	//print("OUTPUT = ".$output);
	
	
	if($errNum == 0) {
		//Login Successful
		//if member profile locked send to proper screen
		
		//die("NO ERRORS");
		
		if($wsResponse[$wsIndices['KEY'][0]]['value'] == "MEMBER PROFILE LOCKED") {
			header("location: blockUserView.php");
		} 
		else {
			///set up session variables
			//SESS_PERSONAL_ID is like doctor id nurse id .....depending on type
			session_regenerate_id();		
			$_SESSION['SESS_MEMBER_ID'] = $wsResponse[$wsIndices['MEMBERID'][0]]['value'];
			$_SESSION['SESS_FIRST_NAME'] = $wsResponse[$wsIndices['FIRSTNAME'][0]]['value'];			
			$_SESSION['SESS_LAST_NAME'] = $wsResponse[$wsIndices['LASTNAME'][0]]['value'];
			$_SESSION['SESS_TYPE'] = $wsResponse[$wsIndices['TYPE'][0]]['value'];
			$_SESSION['SESS_USERNAME'] = $wsResponse[$wsIndices['USERNAME'][0]]['value'];
			$_SESSION['SESS_NEED_APPROVAL'] = $wsResponse[$wsIndices['NEEDAPPROVAL'][0]]['value'];
			$_SESSION['SESS_PERSONAL_ID'] = $wsResponse[$wsIndices['PERSONALID'][0]]['value'];
			$_SESSION['SESS_AUTH_KEY'] = $key;
			//session_write_close();
			//print($output);
			header("location: memberProfileView.php");
		}
		exit();
		//Login failed
			
	}else {
	
			//login failed...output error to screen
			$ct = 0;
			while($ct < $errNum){
				$errmsg_arr[] = $wsResponse[$wsIndices['ERROR'][$ct]]['value'];
				$ct += 1;
			}		
			$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
			session_write_close();
			header("location: index.php");
			exit();
	}

?>
	