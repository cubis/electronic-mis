<?php
//headers for session
session_start();
require_once('auth.php');
require_once('bootstrap.php');

		
		$errmsg_arr = array();

		$url = "http://localhost/emis/emis-dev3/changePassREST.php";
		$fields = array(
			'u' => urlencode($_SESSION['SESS_USERNAME']),
			'key' => urlencode($_SESSION['SESS_AUTH_KEY']),
			'oldpass' => urlencode($_POST['oldpass']),
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

		
		print("OUTPUT = " . $output);
		
		
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
		session_write_close();
		header("location: changePassView.php"); 
?>
  