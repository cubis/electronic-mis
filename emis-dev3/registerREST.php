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

function outputXML($resultMsg, $errNum, $errMsgArr, $db) {
/* @var $AUTH_KEY A key that will be used to prove authentication occurred from this service. */
	/*$controlString = "3p1XyTiBj01EM0360lFw";
	$AUTH_KEY = md5($user.$pw.$controlString);
	
	*/	
	$outputString = ''; //start empty
	$outputString .= "<?xml version=\"1.0\"?>\n";
	$outputString .= "<content><result>" . $resultMsg . "</result>\n";
	$outputString .= "<errNum>" . $errNum . "</errNum>\n";
	if($resultMsg == '1' ){
		
	} else {
		$ct = 0;
		while($ct < $errNum){
			$outputString .= "<ERROR>" . $errMsgArr[$ct] . "</ERROR>\n";
			$ct++;
		}
		$outputString .="<message>" . $msg . "</message>\n";
		logToDB($_GET['u'] . " unsuccessful register", false, -1, $db);
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
		$errNum += 1;
	}
	if (!isset($_POST['lname']) || $_POST['lname'] == '') {
		$errMsgArr[] = 'Last name missing';
		$errNum += 1;
	}
	//test
	if (!isset($_POST['bday']) || $_POST['bday'] == '') {
		$errMsgArr[] = 'birthdate name missing';
		$errNum += 1;
	}
	if (!isset($_POST['email']) || $_POST['email'] == '') {
		$errMsgArr[] = 'e-mail address missing';
		$errNum += 1;
	}
	if (!isset($_POST['ssn']) || $_POST['ssn'] == '') {
		$errMsgArr[] = 'Social Security Number missing';
		$errNum += 1;
	}
	//end
	if (!isset($_POST['u']) || $_POST['u'] == '') {
		$errMsgArr[] = 'Login ID missing';
		$errNum += 1;
	}
	if (!isset($_POST['p']) || $_POST['p'] == '' || $_POST['p'] == 'd41d8cd98f00b204e9800998ecf8427e') {
		$errMsgArr[] = 'Password missing';
		$errNum += 1;
	}
	if (!isset($_POST['cp']) || $_POST['cp'] == '' || $_POST['cp'] == 'd41d8cd98f00b204e9800998ecf8427e') {
		$errMsgArr[] = 'Confirm password missing';
		$errNum += 1;
	}
	if (strcmp($password, $cpassword) != 0) {
		$errMsgArr[] = 'Passwords do not match';
		$errNum += 1;
	}
	if (!ctype_alnum($password)) {
		$errMsgArr[] = 'Password should be numbers & Digits only';
		$errNum += 1;
	}
	if (strlen($password) < 7) {
		$errMsgArr[] = 'Password must be at least 7 chars';
		$errNum += 1;
	}
	if (strlen($password) > 20) {
		$errMsgArr[] = 'Password must be at most 20 chars ';
		$errNum += 1;
	}
	if (!preg_match('`[A-Z]`', $password)) {
		$errMsgArr[] = 'Password must contain at least one upper case';
		$errNum += 1;
	}
	if (!preg_match('`[a-z]`', $password)) {
		$errMsgArr[] = 'Password must contain at least one lower case';
		$errNum += 1;
	}
	if (!preg_match('`[0-9]`', $password)) {
		$errMsgArr[] = 'Password must contain at least one digit';
		$errNum += 1;
	}	
	
	$prepUsers = $db->prepare("SELECT * FROM `Users` WHERE UserName = :id ; ");	

	if ( $prepUsers->execute( array( ":id" => $user ) ) ) {	
		//IF NAME IS NOT IN USE
		if($prepUsers->rowCount() != 0){
			$errMsgArr[] = 'Username already in use';
			$errNum += 1;
		} 
	} else {
		$error = $prep->errorInfo();
		$errMsgArr[] = $error[2];
		$errNum += 1;
		$retVal = outputXML('0', $errNum, $errMsgArr, $db);		
	}
	
	
	
	
	
	if($errNum == 0){
	
			if (strcmp($_POST['type'], "patient") == 0){
				$prep = $db->prepare("INSERT INTO Users(FirstName, LastName, UserName, Email, Birthday, SSN, Type, NeedApproval, Password) 
					VALUES(:fname, :lname, :login, :email, :bday, :ssn, :type, :needapproval, :password)");
				$prep->bindParam(':type', '1');
				$prep->bindParam(':needapproval', '0');					
			} elseif (strcmp($_POST['type'], "nurse") == 0){
				$prep = $db->prepare("INSERT INTO Users(FirstName, LastName, UserName, Email, Birthday, SSN, Type, NeedApproval, Password) 
					VALUES(:fname, :lname, :login, :email, :bday, :ssn, :type, :needapproval, :password)");
				$prep->bindParam(':type', '200');
				$prep->bindParam(':needapproval', '1');
			} elseif (strcmp($_POST['type'], "doctor") == 0){
				$prep = $db->prepare("INSERT INTO Users(FirstName, LastName, UserName, Email, Birthday, SSN, Type, NeedApproval, Password) 
					VALUES(:fname, :lname, :login, :email, :bday, :ssn, :type, :needapproval, :password)"); 
				$prep->bindParam(':type', '300');
				$prep->bindParam(':needapproval', '1');
			} elseif (strcmp($_POST['type'], "admin") == 0){
				$prep = $db->prepare("INSERT INTO Users(FirstName, LastName, UserName, Email, Birthday, SSN, Type, NeedApproval, Password) 
					VALUES(:fname, :lname, :login, :email, :bday, :ssn, :type, :needapproval, :password)");
				$prep->bindParam(':type', '300');
				$prep->bindParam(':needapproval', '1');
			}			
			$prep->bindParam(':fname', $fname);
			$prep->bindParam(':lname', $lname);
			$prep->bindParam(':user', $user);
			$prep->bindParam(':email', $email);
			$prep->bindParam(':bday', $bday);
			$prep->bindParam(':ssn', $ssn);			
			$prep->bindParam(':password', md5($_POST['p']) );
			
			
			$retVal = outputXML('1', $errNum, $errMsgArr $db);
	} else {
		$retVal = outputXML('0', $errNum, $errMsgArr, $db);
	}
	
			
	return $retVal;	
}
	
	
	$output = doService($db);
	
	print($output);

?>