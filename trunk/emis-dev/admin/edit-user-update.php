<?php
    //headers for session
    session_start();
    
    
   $url = "http://localhost/emis/emis-dev/admin/editUserInfoREST.php";
   $fields = array(
	'u' => urlencode($_SESSION['SESS_USERNAME']),
	'key' => urlencode($_SESSION['SESS_AUTH_KEY']),
	'ID' => urlencode($_POST['ID']),
	'Firstname' => urlencode($_POST['Firstname']),
	'Lastname' => urlencode($_POST['Lastname']),
	'Sex' => urlencode($_POST['Sex']),
	'Email' => urlencode($_POST['Email']),
	'Birthday' => urlencode($_POST['Birthday']),
	'Phonenumber' => urlencode($_POST['Phonenumber']),
	'SSN' => urlencode($_POST['SSN']),
	'Type' => urlencode($_POST['Type']),
	'Need' => urlencode($_POST['Need']),
	'Status' => urlencode($_POST['Status'])
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
	
	

	
	$result = $wsResponse[$wsIndices['RESULT'][0]]['value'];
	$msg = $wsResponse[$wsIndices['MESSAGE'][0]]['value'];
	$id = $_POST['ID'];
	header("location: ../admin/edit-user-form.php?ID=$id&msg=$msg");

	exit();
?>
