<?php
//headers for session
session_start();
require_once('auth.php');
require_once('bootstrap.php');

		
		$errmsg_arr = array();
		global $currentPath;
		$url = $currentPath . "visitREST.php";
		//formating stuff for array;
		$fname;
		$fname = $_FILES['uploadfile']['name'];
		$tmpname = $_FILES['uploadfile']['tmp_name'];
		$fsize = $_FILES['uploadfile']['size'];
		$ftype = $_FILES['uploadfile']['type'];
		//$fname = addslashes($fname);
		//$apNum = $_GET['ID'];
		//test the type
		$fp = fopen($tmpname,'rb');
		$content = fread($fp,filesize($tmpname));
		$content = addslashes($content);
		//$fclose($fp);

		if(!get_magic_quotes_gpc()){
			$fname = addslashes($fname);
		}
		//put stuff in array
		$fields = array(
			'u' => urlencode($_SESSION['SESS_USERNAME']),
			'key' => urlencode($_SESSION['SESS_AUTH_KEY']),
			'aid' => urlencode($_POST['aid']),
			'bp' => urlencode($_POST['BP']),
			'weight' => urlencode($_POST['Weight']),
			'symptoms' => urlencode($_POST['Symptoms']),
			'diagnosis' => urlencode($_POST['Diagnosis']),
			'medicine' => urlencode($_POST['Medicine']),
			'dosage' => urlencode($_POST['Dosage']),
			'startDate' => urlencode($_POST['StartDate']),
			'endDate' => urlencode($_POST['EndDate']),
			'totalBill' => urlencode($_POST['Bill']),
			'fname' => urlencode($fname),
			'fsize' => urlencode($fsize),
			'ftype' => urlencode($ftype),
			'content' => urlencode($content)
		);
		
		if(isset($_POST['aid'])){
			$fields['aid'] = urlencode($_POST['aid']);
		}
    
		foreach($fields as $key=>$value){
			$field_string .= $key.'='.$value.'&';
		}
		$field_string = rtrim($field_string, '&');
		
		//die($field_string);
		
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
	die($output);

		$errNum = $wsResponse[$wsIndices['ERRNUM'][0]]['value'];
		
		
		if($errNum == 0){
			$errmsg_arr[] = "Appointment Updated Successfully";
			$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
			$thisAid = $wsResponse[$wsIndices['APPTID'][0]]['value'];
			header("location: apptEditView.php?aid=$thisAid"); 
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
 