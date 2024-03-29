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

function outputXML($errNum, $errMsgArr, $memberInfo) {
/* @var $AUTH_KEY A key that will be used to prove authentication occurred from this service. */
	/*$controlString = "3p1XyTiBj01EM0360lFw";
	$AUTH_KEY = md5($user.$pw.$controlString);
	
	*/
	global $db;
	if(isset($_GET['u'])){
			$user = $_GET['u'];
	} else {
		$user = "UNKOWN";
	}
	
	//START FORMATTING STRING; WRAP CONTENT TAG AROUND ENTIRE MESSAGE
	$outputString = ''; //start empty
	$outputString .= "<?xml version=\"1.0\"?>\n";
	$outputString .= "<content>\n";
	$outputString .= "<errNum>" . $errNum . "</errNum>\n";
	if($errNum == 0){	 
		//IF MEMBER PROFILE LOCKED OUTPUT LOCKED AS THE KEY
		if($memberInfo['Locked'] == 1){
			$outputString .="<key>MEMBER PROFILE LOCKED</key>\n";
			logToDB($memberInfo['UserName'] . " tried to login to locked account", NULL, $user);
		} else {			
			//CREATE AUTH KEY AND GRAB ALL PERSONAL INFO FROM THE USER TABLE
			$outputString .= "<key>" . $memberInfo['AUTHKEY'] . "</key>\n";
			$outputString .="<MemberID>" . $memberInfo['PK_member_id'] . "</MemberID>\n";
			$outputString .="<FirstName>" .$memberInfo['FirstName'] . "</FirstName>\n";
			$outputString .="<LastName>" .$memberInfo['LastName'] . "</LastName>\n";
			$outputString .="<Type>" .$memberInfo['Type'] . "</Type>\n";
			$outputString .="<UserName>" .$memberInfo['UserName'] . "</UserName>\n";
			$outputString .="<NeedApproval>" .$memberInfo['NeedApproval'] . "</NeedApproval>\n";
			$outputString .= "<PersonalID>". $memberInfo['PersonalID'] ."</PersonalID>";			
			
			//log successful login to the database
			logToDB($memberInfo['UserName'] . " successfully logged in", $memberInfo['PK_member_id'], $user);	
		}		
	} 	
	//INCORRET USERNAME OR PASSWORD
	else {	
		//run through error array and output into xml
		
		$ct = 0;
		while($ct < $errNum){
			$outputString .="<ERROR>" . $errMsgArr[$ct] . "</ERROR>\n";
			$ct += 1;
		}
		logToDB($user . " unsuccessful login", NULL, $user);
	}		
	$outputString .= "</content>";	
	return $outputString;	
}

function doService() {
	
	global $db;
	$errMsgArr = array();
	$errNum = 0;
	
	if(!isset($_GET['u']) || $_GET['u'] ==''){
		$errMsgArr[] = 'Login ID Missing';
		$errNum += 1;
	}	
	if(!isset($_GET['p']) || $_GET['p'] =='' || $_GET['p'] == 'd41d8cd98f00b204e9800998ecf8427e'){
		$errMsgArr[] = 'Password Missing';
		$errNum += 1;
	}

	$user = strtoupper($_GET['u']);
	$pw = $_GET['p'];

	// // CHECK FOR MULTIPLE SERVICE REQESTS FROM IP, DENY IF > 3/MINUTE, ALLOW OTHERWISE
	// $prep = $db->prepare('SELECT TimeStamp FROM LogFiles WHERE SourceIP = ?');
	// if ($prep->execute(array($_SESSION["REMOTE_ADDR"]))) { // if records exist, check them
	//   if($prep->rowCount() >= 1){ // else if no previous records, proceed
	//     $info = $prep->fetchAll();
	//     $currentTime = end($info);
	//     $currentTime = date_create_from_format('Y-m-d H:i:s', $currentTime[0]);
	//     $cutOffTime = $currentTime->modify("-1 minutes");
	//     $count = 0;
	//     foreach($info as &$record) {
	//       if(date_create_from_format('Y-m-d H:i:s', $record[0]) > $cutOffTime) {
	// 	$count++;
	//       }
	//     }
	//     if($count > 3) {
	//       $errMsgArr[] = 'Too many login attempts';
	//       $errNum += 1;
	//       $retVal = outputXML($errNum, $errMsgArr, '');
	//       return $retVal;
	//     }
	//   }
	// } else {
	//   $error = $prep->errorInfo();
	//   $errMsgArr[] = $error[2];
	//   $errNum += 1;
	//   return outputXML($errNum, $errMsgArr, '', $db);
	// }

	// CHECK FOR MULTIPLE SERVICE REQESTS FROM MEMBER_ID, DENY IF > 5/MINUTE, ALLOW OTHERWISE
	// FIND MEMEBER_ID FOR CURRENT ATTEMPTED USER
	$prep = $db->prepare('SELECT PK_member_id FROM Users WHERE UserName = ?');
	$id = '';
	if($prep->execute(array($user))) {
	  $id = $prep->fetch();
	  $id = $id[0];
	} else {
	  $error = $prep->errorInfo();
	  $errMsgArr[] = $error[2];
	  $errNum += 1;
	  return outputXML($errNum, $errMsgArr, '', $db);
	}
	
	// SEARCH FOR PREVIOUS LOGIN ATTEMPTS BY USER
	$prep = $db->prepare('SELECT TimeStamp FROM LogFiles WHERE UserName = ?');
	if ($prep->execute(array($user))) {
	  if($prep->rowCount() >= 1){ // if records exist, check them
	    $info = $prep->fetchAll();
	    $currentTime = date_create();
	    $cutOffTime = $currentTime->modify("-12 minutes");
	    $count = 0;
	    foreach($info as &$record) {
	      // $errMsgArr[] = $record['TimeStamp'] . " compared to " . $cutOffTime->format('Y-m-d H:i:s');
	      // $errNum += 1;
	      if(date_create_from_format('Y-m-d H:i:s', $record['TimeStamp']) > $cutOffTime) {
		// $errMsgArr[] = $record['TimeStamp'] . " is greater than " . $cutOffTime->format('Y-m-d H:i:s');
		// $errNum += 1;
		$count++;
	      }
	    }
	    if($count > 5) { // IF MORE THAN 5 ATTEMPTS IN A MINUTE, DENY ACCESS
	      $errMsgArr[] = "Too many login attempts $count, $id, $user";
	      $errNum += 1;
	      $retVal = outputXML($errNum, $errMsgArr, '');
	      return $retVal;
	    }
	  }
	}
	else {
	  $error = $prep->errorInfo();
	  $errMsgArr[] = $error[2];
	  $errNum += 1;
	  return outputXML($errNum, $errMsgArr, '', $db);
	}
	
	$prep = $db->prepare("SELECT * FROM `Users` WHERE UserName = :id AND Password = :pw ; ");

	//LOOK FOR USERNAME AND PW IN DATABASE THEN CALL OUTPUTXML BASED ON RESULTS
	if ($prep->execute(array(":id" => $user, ":pw" => $pw))) {
		if($prep->rowCount() == 1){
			$memberInfo = $prep->fetch(PDO::FETCH_ASSOC);
			
			//CREATE AUTH KEY BASED ON USERNAME . PASSWORD . CONTROL STRING . CURRENT TIME
			//AND SAVE IN THE DATABASE
			$controlString = "3p1XyTiBj01EM0360lFw";
			$user = strtoupper($memberInfo['UserName']);
			$pw = $memberInfo['Password'];
			$AUTH_KEY = md5($user.$pw.$controlString.date("H:i:s"));
			$db->exec("UPDATE Users SET CurrentKey='" . $AUTH_KEY . "' WHERE PK_member_id='" . $memberInfo['PK_member_id'] . "'");
			$memberInfo['AUTHKEY'] = $AUTH_KEY;
			
			//DEPENDING ON TYPE GRAB THEIR PERSONAL ID
			$qry = "SELECT * FROM ";
			if($memberInfo['Type'] == 1){
				$qry .= "`Patient` ";
				$assocString = 'PK_PatientID';				
			} else if($memberInfo['Type'] == 200){
				$qry .= "`Nurse` ";
				$assocString = 'PK_NurseID';				
			} else if($memberInfo['Type'] == 300){
				$qry .= "`Doctor` ";
				$assocString = 'PK_DoctorID';				
			} else if($memberInfo['Type'] == 400){
				$qry .= "Admin ";
				$assocString = 'PK_AdminID';							
			}			
			$qry .= " WHERE FK_member_id = :id";
			//print($qry . " ID[" . $memberInfo['PK_member_id'] . "]");
			$prep = $db->prepare($qry);
			
			if ( $prep->execute(array( ":id" => $memberInfo['PK_member_id'] ))) {
			//die("PERSONAL ID: " . $prep->rowCount());
				
					$info = $prep->fetch(PDO::FETCH_ASSOC);
					$memberInfo['PersonalID'] = $info[$assocString];			
			} 
			else {				
				$error = $prep->errorInfo();
				$errMsgArr[] = $error[2];
				$errNum += 1;
				return outputXML($errNum, $errMsgArr, '');		
			}		
			
			
			$retVal = outputXML($errNum, $errMsgArr, $memberInfo);
		} else {
			$errMsgArr[] = 'Login and Password Incorrect';
			$errNum += 1;
			$retVal = outputXML($errNum, $errMsgArr, '');
		}
	} else {
		$error = $prep->errorInfo();
		$errMsgArr[] = $error[2];
		$errNum += 1;
		$retVal = outputXML($errNum, $errMsgArr, '');		
	}	
	return $retVal;	
}
	
	clean(&$_GET);
	
	$output = doService();
	
	print($output);

?>