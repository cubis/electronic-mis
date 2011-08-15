<?php

require_once('configREST.php');     //sql connection information
require_once('bootstrapREST.php');  //link information

function outputXML($errNum, $errMsgArr, $memberInfo, $medPrep, $precPrep) {
    /* @var $AUTH_KEY A key that will be used to prove authentication occurred from this service. */
    if (isset($_GET['u'])) {
        $user = $_GET['u'];
    } else {
        $user = "UNKOWN";
    }
    if (isset($_GET['pat'])) {
        $target = $_GET['pat'];
    } else {
        $target = 'all';
    }


    $outputString = ''; //start empty
    $outputString .= "<?xml version=\"1.0\"?>\n";
    $outputString .= "<content><errNum>" . $errNum . "</errNum>\n";
    if ($errNum == 0) {
	if(!($medPrep == '')){
		$outputString .= "<MEDCOUNT>" . $medPrep->rowCount() . "</MEDCOUNT>\n";
		while ($medArray = $medPrep->fetch(PDO::FETCH_ASSOC)) {
			$outputString .= "<MedInfo>";
			$outputString .= "<MedID>" . $medArray['PK_MedicationsID'] . "</MedID>\n";
			$outputString .= "<Medication>" . $medArray['Medication'] . "</Medication>\n";
			$outputString .= "<Dosage>" . $medArray['Dosage'] . "</Dosage>\n";
			$outputString .= "<StartDate>" . $medArray['StartDate'] . "</StartDate>\n";
			$outputString .= "<EndDate>" . $medArray['EndDate'] . "</EndDate>\n";
			$outputString .= "</MedInfo>";
		}
	}
	if(!($precPrep == '')){
		$outputString .= "<PRECCOUNT>" . $precPrep->rowCount() . "</PRECCOUNT>\n";
		while ($precArray = $precPrep->fetch(PDO::FETCH_ASSOC)) {
			$outputString .= "<PrecInfo>";
			$outputString .= "<PrecID>" . $precArray['PK_ConditionID'] . "</PrecID>\n";
			$outputString .= "<ConditionDesc>" . $precArray['Description'] . "</ConditionDesc>\n";
			$outputString .= "</PrecInfo>";
		}
	}
	
	logToDB($user . " access patient medical info for " . $target, $memberInfo['PK_member_id'], $user);
	
    } else {
        $ct = 0;
        while ($ct < $errNum) {
            $outputString .= "<ERROR>" . $errMsgArr[$ct] . "</ERROR>\n";
            $ct++;
        }
        if (!isset($memberInfo['PK_member_id'])) {
            logToDB($user . " failed to access patient medical info for " . $target, NULL, $user);
        } else {
            logToDB($user . " failed to access patient medical info for " . $target, $memberInfo['PK_member_id'], $user);
        }
	
    }

    $outputString .= "</content>";
   return $outputString;
    
  //  return "STUFF";
   
}

function doService() {

    global $db;

    $errMsgArr = array();
    $errNum = 0;


    //MAKE SURE THEY PASSED US CREDENTIALS	
    if (!isset($_GET['u']) || $_GET['u'] == '') {
        $errMsgArr[] = "No username provided for authentication";
        $errNum++;
    }
    if (!isset($_GET['key']) || $_GET['key'] == '') {
        $errMsgArr[] = "No key provided for authentication";
        $errNum++;
    }
    if ($errNum != 0) {
        return outputXML($errNum, $errMsgArr, '', '', '');
    }


    //USE CREDENTIALS AND AUTHENTICATE
    
    $user = $_GET['u'];
    $recKey = $_GET['key'];
    $userInfoPrep = $db->prepare("SELECT * FROM Users WHERE UserName = :user;");
    $userInfoSuccess = $userInfoPrep->execute(array(":user" => $user));
    $memberInfo = $userInfoPrep->fetch(PDO::FETCH_ASSOC);
    //failed to access database for user info
    if (!$userInfoSuccess) {
        $errMsgArr[] = "DATABASE ERROR ONE";
        $errNum++;
        return outputXML($errNum, $errMsgArr, '', '', '');
    }
    $currKey = $memberInfo['CurrentKey'];
    $trustString = "xolJXj25jlk56LJkk5677LS";
    $trustedKey = md5($currKey . $trustString);


    if ($recKey == $trustedKey || $recKey == $currKey){// || $memberInfo['Type'] >= 200) {
	
	
	if(isset($_GET['med'])){
		$medQry = "SELECT Medications.* FROM Medications WHERE ";
		$paramArray = array();
		if($memberInfo['Type'] == 1 || isset($_GET['pat'])){
			$medQry .= "Medications.FK_PatientID = :patID AND ";
			if($memberInfo['Type'] == 1){
				$patIDQry = "SELECT Patient.PK_PatientID FROM Patient WHERE Patient.FK_member_id = " . $memberInfo['PK_member_id'];
				$patIDPrep = $db->prepare($patIDQry);
				$updateSucc = $patIDPrep->execute();
				if (!$updateSucc) {
					$errorInfoArray = $prep->errorInfo();
					$errMsgArr[] = $errorInfoArray[2];
					$errNum++;					
					return outputXML($errNum, $errMsgArr, $memberInfo, '', '');
				}
				$thisPatID = $patIDPrep->fetch(PDO::FETCH_ASSOC);	
				$paramArray[":patID"] = $thisPatID['PK_PatientID'];
			} else {
				$paramArray[":patID"]=$_GET["pat"];
			}
		}
		$paramArray[":med"] = $_GET['med'];
		
		$medQry .= "Medications.PK_MedicationsID = :med";
		$medPrep = $db->prepare($medQry);
		$medSuccess = $medPrep->execute( $paramArray );
	
		if (!$medSuccess) {
			$errorInfoArray = $medPrep->errorInfo();
			$errMsgArr[] = $errorInfoArray[2];
			$errNum++;					
		}
	
		return outputXML($errNum, $errMsgArr, $memberInfo, $medPrep, '');
	//	return "STUFF";
		
	
	}
	if(isset($_GET['prec'])){
		$precQry = "SELECT Precondition.* FROM Precondition WHERE ";
		$paramArray = array();
		if($memberInfo['Type'] == 1 || isset($_GET['pat'])){
			$precQry .= "Precondition.FK_PatientID = :patID AND ";
			if($memberInfo['Type'] == 1){
				$patIDQry = "SELECT Patient.PK_PatientID FROM Patient WHERE Patient.FK_member_id = " . $memberInfo['PK_member_id'];
				$patIDPrep = $db->prepare($patIDQry);
				$updateSucc = $patIDPrep->execute();
				if (!$updateSucc) {
					$errorInfoArray = $prep->errorInfo();
					$errMsgArr[] = $errorInfoArray[2];
					$errNum++;					
					return outputXML($errNum, $errMsgArr, $memberInfo, '', '');
				}
				$thisPatID = $patIDPrep->fetch(PDO::FETCH_ASSOC);
				$paramArray[":patID"] = $thisPatID['PK_PatientID'];
			} else {
				$paramArray[":patID"]=$_GET["pat"];
			}	
		}
		
		$paramArray[":prec"] =$_GET['prec'];
		$precQry .= "Precondition.PK_ConditionID = :prec";
		$precPrep = $db->prepare($precQry);
		$precSuccess = $precPrep->execute( $paramArray );
	
		if (!$precSuccess) {
			$errorInfoArray = $precPrep->errorInfo();
			$errMsgArr[] = $errorInfoArray[2];
			$errNum++;					
		}
	
		return outputXML($errNum, $errMsgArr, $memberInfo, '', $precPrep);
	
	
	}
	$medQry = "SELECT Medications.* FROM Medications";
	$precQry = "SELECT Precondition.* FROM Precondition";
	if($memberInfo['Type'] == 1 || isset($_GET['pat'])){
		$medQry .= " WHERE Medications.FK_PatientID = :patID";
		$precQry .= " WHERE Precondition.FK_PatientID = :patID";
		if($memberInfo['Type'] == 1){
			$patIDQry = "SELECT Patient.PK_PatientID FROM Patient WHERE Patient.FK_member_id = " . $memberInfo['PK_member_id'];
			$patIDPrep = $db->prepare($patIDQry);
			$updateSucc = $patIDPrep->execute();
			if (!$updateSucc) {
				$errorInfoArray = $prep->errorInfo();
				$errMsgArr[] = $errorInfoArray[2];
				$errNum++;					
				return outputXML($errNum, $errMsgArr, $memberInfo, '', '');
			}
			$thisPatID = $patIDPrep->fetch(PDO::FETCH_ASSOC);	
			$paramArray = array(":patID"=>$thisPatID['PK_PatientID']);
		} else {
			$paramArray = array(":patID"=>$_GET["pat"]);
		}
	}

        $medPrep = $db->prepare($medQry);
        $precPrep = $db->prepare($precQry);
        $medSuccess = $medPrep->execute( $paramArray );
        $precSuccess = $precPrep->execute( $paramArray );

       if (!$medSuccess) {
		$errorInfoArray = $medPrep->errorInfo();
		$errMsgArr[] = $errorInfoArray[2];
		$errNum++;					
        }
	 if (!$precSuccess) {
		$errorInfoArray = $precPrep->errorInfo();
		$errMsgArr[] = $errorInfoArray[2];
		$errNum++;					
        }
	
        if($errNum == 0) {
		$retVal = outputXML($errNum, $errMsgArr, $memberInfo, $medPrep, $precPrep);
        } 
	else {
		$retVal = outputXML($errNum, $errMsgArr, $memberInfo, '', '');
        }
	
	
    } else {
        $errMsgArr[] = "Unauthorized to view information";
        $errNum++;
        $retVal = outputXML($errNum, $errMsgArr, '');
    }
   

   return $retVal;
}

//8758e4c115ba4669e13a574464488496xolJXj25jlk56LJkk5677LS
//AUTH KEY 40fc9157068b426ea62b1134d57be6ce
// set up some useful variables
$output = doService();

print($output);
//print("SHIT = " . $_GET['u']);
?>