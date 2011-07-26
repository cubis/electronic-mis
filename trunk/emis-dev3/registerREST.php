<?php
/* Assumptions:  transfer is over secure https
 *               password is already hashed+salt before transfer
 *
 * Access:  This WS may be accessed by anyone
 * Input:  https://[URL]/Authenticate.php?u=[username]&password=[pass]
 * Output: XML
 *   result  [0 or 1]  if user and pass is correct
 *   key     [hashed key] validate auth was performed on this WS
 *
 *
 ****EXAMPLE OUTPUT DIGEST*****
 *  <?xml version="1.0"?>
 *      <result>    1       </result>
 *      <key>       fb504a91465213203ae7c3866bbf3cf4</key>
 *      <userID>    12345   </userID>
 *      <AccessType>400     </type>
 *
 *  */




require_once('configREST.php');     //sql connection information
require_once('bootstrapREST.php');  //link information

function outputXML($errNum, $errMsgArr) {


/* @var $AUTH_KEY A key that will be used to prove authentication occurred from this service. */
	/*$controlString = "3p1XyTiBj01EM0360lFw";
	$AUTH_KEY = md5($user.$pw.$controlString);
	
	*/	
	global $db;
	if(isset($_POST['u'])){
			$user = $_POST['u'];
	} else {
		$user = "UNKOWN";
	}
	
	$outputString = ''; //start empty
	$outputString .= "<?xml version=\"1.0\"?>\n";
	$outputString .= "<content>\n";
	$outputString .= "<errNum>" . $errNum . "</errNum>\n";
	if($errNum == 0){
		$outputString .= "<RESULT>SUCCESSFUL REGISTER!</RESULT>";
		logToDB($user . " successfuly registered", NULL, $user);
	} else {
		$ct = 0;
		while($ct < $errNum){
			$outputString .= "<ERROR>" . $errMsgArr[$ct] . "</ERROR>\n";
			$ct++;
		}
		logToDB($user . " unsuccessful registered", NULL, $user);
	}		
	$outputString .= "</content>";	
	return $outputString;	
}

function doService($db) {

	$errMsgArr = array();
	$errNum = 0;

	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$bday = $_POST['bday'];
	$email = $_POST['email'];
	$ssn = $_POST['ssn'];
	$user = $_POST['u'];
	$password = $_POST['p'];
	$cpassword = $_POST['cp'];
	$type = $_POST['type'];
	
	//Input Validations
	if (!isset($_POST['fname']) || $_POST['fname'] == '') {
		$errMsgArr[] = 'First name missing';
		$errNum++;
	}
	if (!isset($_POST['lname']) || $_POST['lname'] == '') {
		$errMsgArr[] = 'Last name missing';
		$errNum++;
	}
	//test
	if (!isset($_POST['bday']) || $_POST['bday'] == '') {
		$errMsgArr[] = 'birthdate missing';
		$errNum++;
	}
	if (!isset($_POST['email']) || $_POST['email'] == '') {
		$errMsgArr[] = 'e-mail address missing';
		$errNum++;
	}
	if (!isset($_POST['ssn']) || $_POST['ssn'] == '') {
		$errMsgArr[] = 'Social Security Number missing';
		$errNum++;
	}
	//end
	if (!isset($_POST['u']) || $_POST['u'] == '') {
		$errMsgArr[] = 'Login ID missing';
		$errNum++;
	}
	if (!isset($_POST['p']) || $_POST['p'] == '' || $_POST['p'] == 'd41d8cd98f00b204e9800998ecf8427e') {
		$errMsgArr[] = 'Password missing';
		$errNum++;
	}
	if (!isset($_POST['cp']) || $_POST['cp'] == '' || $_POST['cp'] == 'd41d8cd98f00b204e9800998ecf8427e') {
		$errMsgArr[] = 'Confirm password missing';
		$errNum++;
	}
	if (strcmp($password, $cpassword) != 0) {
		$errMsgArr[] = 'Passwords do not match';
		$errNum++;
	}
	if (!ctype_alnum($password)) {
		$errMsgArr[] = 'Password should be numbers and digits only';
		$errNum++;
	}
	if (strlen($password) < 7) {
		$errMsgArr[] = 'Password must be at least 7 chars';
		$errNum++;
	}
	if (strlen($password) > 20) {
		$errMsgArr[] = 'Password must be at most 20 chars ';
		$errNum++;
	}
	if (!preg_match('`[A-Z]`', $password)) {
		$errMsgArr[] = 'Password must contain at least one upper case';
		$errNum++;
	}
	if (!preg_match('`[a-z]`', $password)) {
		$errMsgArr[] = 'Password must contain at least one lower case';
		$errNum++;
	}
	if (!preg_match('`[0-9]`', $password)) {
		$errMsgArr[] = 'Password must contain at least one digit';
		$errNum++;
	}	
	
		$prepUsers = $db->prepare("SELECT * FROM `Users` WHERE UserName = :id ; ");	

		if ( $prepUsers->execute( array( ":id" => $user ) ) ) {	
			//IF NAME IS NOT IN USE
			if($prepUsers->rowCount() != 0){
				$errMsgArr[] = 'Username already in use';
				$errNum++;
			} 
		} else {
			$error = $prepUsers->errorInfo();
			$errMsgArr[] = $error[2];
			$errNum++;
			$retVal = outputXML($errNum, $errMsgArr);		
		}
	
	
	
	
	
	if($errNum == 0){
			
			
			//set up and insert values into the user table
			$insertUserPrep = $db->prepare("INSERT INTO Users(FirstName, LastName, UserName, Email, Birthday, SSN, Type, NeedApproval, Password) 
					VALUES(:fname, :lname, :login, :email, :bday, :ssn, :type, :needapproval, :password);");
			$tableType = '';
			
			$needapproval;
			$type;

			if (strcmp($_POST['type'], "patient") == 0){				
				$type = 1;
				$needapproval = 0;
				$tableType = "Patient";						
			} elseif (strcmp($_POST['type'], "nurse") == 0){
				$type = 200;
				$needapproval = 1;
				$tableType = "Nurse";
			} elseif (strcmp($_POST['type'], "doctor") == 0){
				$type = 300;
				$needapproval = 1;
				$tableType = "Doctor";
			} elseif (strcmp($_POST['type'], "admin") == 0){
				$type = 400;
				$needapproval = 1;
				$tableType = "Admin";
			}			
			
			$vals = array(  ':type'=>$type,
				':needapproval'=>$needapproval,		
				':fname'=>$fname,
				':lname'=>$lname,
				':login'=>$user,
				':email'=>$email,
				':bday'=>$bday,
				':ssn'=>$ssn,	
				':password'=>md5($password)
			);
			$insertUserSuccess = $insertUserPrep->execute($vals);
			
			if(   !$insertUserSuccess  ){
				//didnt insert into user table
				$errMsgArr[] = 'DATABASE ERROR ONE';
				$errNum++;				
			} 			
			else {
			
				//get the primary key for the recently entered row
				$memIDPrep = $db->prepare("SELECT * FROM Users WHERE UserName = '" . $user . "'");
				$getIDSuccess = $memIDPrep->execute();
				if( ! $getIDSuccess ){
					//get member id error
					$errMsgArr[] = 'DATABASE ERROR TWO';
					$errNum++;
				} else {
					
					//add into the proper sub table with the user primary key as the member foreign key
					$member = $memIDPrep->fetch(PDO::FETCH_ASSOC);
					$insertTypePrep = $db->prepare("INSERT INTO " .$tableType. "(FK_member_id) VALUES('" .$member['PK_member_id']. "')");
					//insert into subtable failed
					if( !($insertTypePrep->execute()) ){
						$errMsgArr[] =  "DATABASE ERORR THREE";
						$errNum++;
					}
				}
			}
			
			$retVal = outputXML($errNum, $errMsgArr);
			
	} else {
		$retVal = outputXML($errNum, $errMsgArr);
	}
	
			
	return $retVal;	
	
}
	
	
	$output = doService($db);
	
	print($output);


?>