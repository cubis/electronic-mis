<?php
require_once('/configREST.php');     //sql connection information
require_once('/bootstrapREST.php');  //link information

function outputXML($result, $key, $numError, $ErrorString) {
/* @var $AUTH_KEY A key that will be used to prove authentication occurred from this service. */
	$controlString = "3p1XyTiBj01EM0360lFw";
	$AUTH_KEY = md5($user.$pw.$controlString);
	
	$outputString = ''; //start empty
	$outputString .= "<?xml version=\"1.0\"?>\n";
	$outputString .= "<content><result>" . $result . "</result>\n";
	
	if($result == '1'){
		$getIDQry = "SELECT * FROM USERS WHERE UserName='".$user."' AND Password='".md5($oldpass)."'";
		$getIDRes = mysql_query($getIDQry);
		$rows = mysql_fetch_assoc($getIDRes);
		$id = $rows['PK_member_id'];
		logToDB($user." changed password", true, $id); 
		
		$outputString .="<key>".$key."</key>\n";	
		
	} else {
		$outputString .="<numerror>".$numError."</numerror>\n";
			$outputString .= "<ERROR>".$ErrorString."</ERROR>\n";			

	}
		
	$outputString .= "</content>";

	return $outputString;
	
}

function doService($url, $method) {
	if($method == 'POST'){
		$user = strtoupper($_POST['u']);
		$qry="SELECT * FROM Users WHERE UserName='" . $user . "'";
		$result=mysql_query($qry);
		$member = mysql_fetch_assoc($result);
		$pwd = $member['Password'];
				
		$trustedKey = "xolJXj25jlk56LJkk5677LS";
		$controlString = "3p1XyTiBj01EM0360lFw";
		
		
		$AUTH_KEY = md5($user.$pwd.$controlString);
		$TRUST_KEY = md5($AUTH_KEY.$trustedKey);
		$postKey = $_POST['key'];
		
		if($postKey == $TRUST_KEY){
			  if (isset($_POST['oldpass'])) {

                                if ($_POST['newpass1'] == $_POST['newpass2']) {
                                    $qry = "SELECT * FROM Users WHERE UserName='$user' AND Password='" . md5($_POST['oldpass']) . "'";
                                    $result = mysql_query($qry);

                                    $oldpass = $_POST['oldpass'];
                                    $newpass1 = $_POST['newpass1'];
                                    $newpass2 = $_POST['newpass2'];
				    $numError = 0;
				    $ErrorString;

                                    //Check whether the query was successful or not
                                    if ($result) {
                                        if (mysql_num_rows($result) == 1) {

                                            //problems with new password
                                            if ($oldpass == $newpass1) {
                                                $errmsg_arr[] = 'New and old passwords must be different';
                                                $ErrorString .= "Error: New and old passwords must be different.\n<br />";
                                                $numError += 1;
                                            }
                                            if (!ctype_alnum($newpass1)) {
                                                $errmsg_arr[] = 'New password should be numbers & Digits only';
                                                 $ErrorString .="Error: New password should be numbers & Digits only.\n<br />";
                                                $numError += 1;
                                            }
                                            if (strlen($newpass1) < 7) {
                                                $errmsg_arr[] = 'New password must be at least 7 chars';
                                                $ErrorString .="Error: New password must be at least 7 chars.\n<br />";
                                                $numError += 1;;
                                            }
                                            if (strlen($newpass1) > 20) {
                                                $errmsg_arr[] = 'New password must be at most 20 chars';
                                                 $ErrorString .="Error: New password must be at most 20 chars.\n<br />";
                                                $numError += 1;
                                            }
                                            if (!preg_match('`[A-Z]`', $newpass1)) {
                                                $errmsg_arr[] = 'New password must contain at least one upper case';
                                                 $ErrorString .="Error: New password must contain at least one upper case.\n<br />";
                                                $numError += 1;
                                            }
                                            if (!preg_match('`[a-z]`', $newpass1)) {
                                                $errmsg_arr[] = 'New password must contain at least one lower case';
                                                $ErrorString .="Error: New password must contain at least one lower case.\n<br />";
                                                $numError += 1;
                                            }
                                            if (!preg_match('`[0-9]`', $newpass1)) {
                                                $errmsg_arr[] = 'New password must contain at least one digit';
                                                 $ErrorString .="Error: New password must contain at least one digit.\n<br />";
                                                $numError += 1;
                                            }
					    
					    if($numError == 0){
						
						$updateQry = "UPDATE Users SET Password='" . md5($newpass1) . "' WHERE UserName='" . $user . "' AND Password='" . md5($oldpass) . "'";
						if(!mysql_query($updateQry)){
							$ErrorString .= mysql_error()."\n<br />";
							$numError += 1;
							$retVal = outputXML('0', -1, $numError, $ErrorString);
						}
						$controlString = "3p1XyTiBj01EM0360lFw";
						$AUTH_KEY = md5(strtoupper($user).md5($newpass1).$controlString);
						$retVal = outputXML('1', $AUTH_KEY, $numError, $ErrorString);
					    } else {
						$retVal = outputXML('0', 'PASSWORD RESET ERROR', $numError, $ErrorString);
					    }

                                        } else {
                                            //Login failed or old password is wrong
                                            $ErrorString .="Error: Old password is wrong.\n<br />";
					    $numError += 1;
					    $retVal = outputXML('0', -1, $numError, $ErrorString);
                                        }
                                    } else {
                                       $ErrorString .= "Result was empty!\n<br />";
				        $numError += 1;
					$retVal = outputXML('0', -1, $numError, $ErrorString);
                                    }
                                } else {				
                                  $ErrorString .=  "New Passwords do not match\n<br />";
                                   $numError += 1;
					$retVal = outputXML('0', -1, $numError, $ErrorString);
                                }
                            }
                            //in case the sql login fails, for debugging
                            else {
				$ErrorString .=  "Old Password Incorrect\n<br />";
				$numError += 1;
				$retVal = outputXML('0', -1, $numError, $ErrorString);
                            }
		} else if($postKey == $AUTH_KEY){
			$ErrorString .= "UNTRUSTED CLIENTS UNABLE TO UPDATE ACCOUNT INFORMATION\n<br />";
				$numError += 1;
			$retVal = outputXML('0', -1, $numError, $ErrorString );
		} else {
			$ErrorString .= "UNAUTHORIZED ACCESS\n<br />";
			$numError += 1;
			$retVal = outputXML('0',  -1, $numError, $ErrorString);
		}
	}else{
		$ErrorString .=  "RECEIVED INCORRECT MESSAGE\n<br />";
		$numError += 1;
		$retVal = outputXML('0',  -1, $numError, $ErrorString);
	}
	
	return $retVal;
}
//8758e4c115ba4669e13a574464488496xolJXj25jlk56LJkk5677LS
//AUTH KEY 40fc9157068b426ea62b1134d57be6ce

// set up some useful variables
$serviceURL = $_SERVER['REQUEST_URI'];
$serviceMethod = strtoupper($_SERVER['REQUEST_METHOD']);
$getArgs = $_GET;
$postArgs = $_POST; //don't care about post
$retVal = doService($serviceURL, $serviceMethod);

	
print($retVal);

?>