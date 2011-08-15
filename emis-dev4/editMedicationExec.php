<?php
//headers for session
session_start();
require_once('auth.php');
require_once('bootstrap.php');

		
		$errmsg_arr = array();
		global $currentPath;
		$url = $currentPath . "editMedicationREST.php";
		$fields = array(
			'u' => urlencode($_SESSION['SESS_USERNAME']),
			'key' => urlencode($_SESSION['SESS_AUTH_KEY']),
			'medication' => urlencode($_POST['medication']),
			'dosage' => urlencode($_POST['dosage']),
			'startdate' => urlencode( $_POST['startYear'] . '-' . $_POST['startMonth'] . '-' . $_POST['startDay']),
			'enddate' => urlencode( $_POST['endYear'] . '-' . $_POST['endMonth'] . '-' . $_POST['endDay']),
			'pat' => urlencode( $_POST['pat'] )
		);
		
		//die($_POST['status']);
		if(isset($_POST['med']) && $_POST['med'] != ''){
			$fields['med'] = urlencode($_POST['med']);
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
	//	print($output);
		
	
		if($errNum == 0){
			$errmsg_arr[] = "Appointment Updated Successfully";
			$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
			$thisAid = $wsResponse[$wsIndices['APPTID'][0]]['value'];
			//header("location: apptEditView.php?aid=$thisAid"); 
			header("location: medInfoView.php?pat=" . $_POST['pat']);
		} else {
			$ct = 0;
			while($ct < $errNum){
				$errmsg_arr[] = $wsResponse[$wsIndices['ERROR'][$ct]]['value'];
				$ct++;
			}
			$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
			header("location: apptEditView.php?med=" . $_POST['med'] . "&pat=" . $_POST['pat']);
		}
		
		session_write_close();
	
?>
  