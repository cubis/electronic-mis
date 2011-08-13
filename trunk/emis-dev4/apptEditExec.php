<?php
//headers for session
session_start();
require_once('auth.php');
require_once('bootstrap.php');

		
		$errmsg_arr = array();
		global $currentPath;
		$url = $currentPath . "editApptREST.php";
		$fields = array(
			'u' => urlencode($_SESSION['SESS_USERNAME']),
			'key' => urlencode($_SESSION['SESS_AUTH_KEY']),
			'status' => urlencode($_POST['status']),
			'reminder' => urlencode($_POST['reminder']),
			'reason' => urlencode($_POST['reason']),
			'time' => urlencode($_POST['time']),
			'date' => urlencode( $_POST['year'] . '-' . $_POST['month'] . '-' . $_POST['day']),
			'doctor' => urlencode($_POST['doctor']),
			'patient' => urlencode($_POST['patient'])
		);
		
		if(isset($_POST['aid'])){
			$fields['aid'] = urlencode($_POST['aid']);
		}
    
		foreach($fields as $key=>$value){
			$field_string .= $key.'='.$value.'&';
		}
		$field_string = rtrim($field_string, '&');
    
    
    
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, sizeof($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);    
		curl_setopt($ch, CURLOPT_TIMEOUT, 8);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		curl_close($ch);
   
		$parser = xml_parser_create();
		
		xml_parse_into_struct($parser, $output, $wsResponse, $wsIndices);
		
	//	print("output = " . $output);
	//	print_r($_POST);

		$errNum = $wsResponse[$wsIndices['ERRNUM'][0]]['value'];
		
		
		if($errNum == 0){
			$errmsg_arr[] = "Appointment Updated Successfully";
			$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
			$thisAid = $wsResponse[$wsIndices['APPTID'][0]]['value'];
			//header("location: apptEditView.php?aid=$thisAid"); 
			header("location: apptView.php");
		} else {
			$ct = 0;
			while($ct < $errNum){
				$errmsg_arr[] = $wsResponse[$wsIndices['ERROR'][$ct]]['value'];
				$ct++;
			}
			$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
			header("location: apptEditView.php");
		}
		
		session_write_close();
		
?>
  