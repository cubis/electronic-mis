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


//Test to see if this is either an new or old appoint
//Wherther there exists an appt id
if($_POST['id'])
	$output = doServiceVi($db);
else
	$output = doServiceAp($db);
	
print($output);


function outputXML($errNum, $errMsgArr, $db) {


/* @var $AUTH_KEY A key that will be used to prove authentication occurred from this service. */
        /*$controlString = "3p1XyTiBj01EM0360lFw";
        $AUTH_KEY = md5($user.$pw.$controlString);
        
        */      
        $outputString = ''; //start empty
        $outputString .= "<?xml version=\"1.0\"?>\n";
        $outputString .= "<content>\n";
        $outputString .= "<errNum>" . $errNum . "</errNum>\n";
        if($errNum == 0){
                $outputString .= "<RESULT>SUCCESSFUL Visit</RESULT>";
                logToDB($_POST['u'] . " successful visit", false, -1, $db);
        } else {
                $ct = 0;
                while($ct < $errNum){
                        $outputString .= "<ERROR>" . $errMsgArr[$ct] . "</ERROR>\n";
                        $ct++;
                }
                logToDB($_POST['u'] . " unsuccessful visit", false, -1, $db);
        }               
        $outputString .= "</content>";  
        return $outputString;   
}

function doServiceVi($db) {

        $errMsgArr = array();
        $errNum = 0;
		$id = $_POST['id'];
        $bp = $_POST['bp'];
        $weight = $_POST['weight'];
        $sym = $_POST['sym'];
        $diag = $_POST['diag'];
        $med = $_POST['med'];
        $dos = $_POST['dos'];
        $sdate = $_POST['sdate'];
        $edate = $_POST['edate'];
        $pp = $_POST['pp'];
		$numon = $_POST['nummonths'];
		$rd = $_POST['rd'];
		$fname = $_POST['fname'];
		$floc = $_POST['floc'];
        
        //Input Validations (still need to do
        if (!isset($_POST['bp']) || $_POST['bp'] == '') {
                $errMsgArr[] = 'Blood Pressure missing';
                $errNum += 1;
        }
        if (!isset($_POST['weight']) || $_POST['weight'] == '') {
                $errMsgArr[] = 'Weight missing';
                $errNum += 1;
        }
        //test
        if (!isset($_POST['sym']) || $_POST['sym'] == '') {
                $errMsgArr[] = 'Symptoms missing';
                $errNum += 1;
        }
        if (!isset($_POST['diag']) || $_POST['diag'] == '') {
                $errMsgArr[] = 'Diagnosis address missing';
                $errNum += 1;
        }
        //end
        
        
        //$prepUsers = $db->prepare("SELECT * FROM `Users` WHERE UserName = :id ; ");     
		/*
        if ( $prepUsers->execute( array( ":id" => $user ) ) ) { 
                //IF NAME IS NOT IN USE
                if($prepUsers->rowCount() != 0){
                        $errMsgArr[] = 'Username already in use';
                        $errNum += 1;
                } 
        } else {
                $error = $prepUsers->errorInfo();
                $errMsgArr[] = $error[2];
                $errNum += 1;
                $retVal = outputXML($errNum, $errMsgArr, $db);          
        }
        */
        
        
        
        
        if($errNum == 0){
                        //set up and insert values into the user table
                        $updateVistPrep = $db->prepare("INSERT INTO Users(FirstName, LastName, UserName, Email, Birthday, SSN, Type, NeedApproval, Password) 
                                        VALUES(:fname, :lname, :login, :email, :bday, :ssn, :type, :needapproval, :password);");
                        //$tableType = '';
                        
                        //$needapproval;
                        //$type;

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
                                $errMsgArr[] = 'Insert into user table failed';
                                $errNum += 1;                           
                        }                       
                        else {
                        
                                //get the primary key for the recently entered row
                                $memIDPrep = $db->prepare("SELECT * FROM Users WHERE UserName = '" . $user . "'");
                                $getIDSuccess = $memIDPrep->execute();
                                if( ! $getIDSuccess ){
                                        $errMsgArr[] = 'Get user table ID failed';
                                        $errNum += 1;
                                } else {
                                        
                                        //add into the proper sub table with the user primary key as the member foreign key
                                        $member = $memIDPrep->fetch(PDO::FETCH_ASSOC);
                                        $insertTypePrep = $db->prepare("INSERT INTO " .$tableType. "(FK_member_id) VALUES('" .$member['PK_member_id']. "')");
                                        if( !($insertTypePrep->execute()) ){
                                                $errMsgArr[] =  "Insert into $tableType table failed";
                                                $errNum += 1;
                                        }
                                }
                        }
                        
                        $retVal = outputXML($errNum, $errMsgArr, $db);
                        
        } else {
                $retVal = outputXML($errNum, $errMsgArr, $db);
        }
        
                        
        return $retVal; 
        
}

function doServiceAp($db) {

        $errMsgArr = array();
        $errNum = 0;

        $bp = $_POST['bp'];
        $weight = $_POST['weight'];
        $sym = $_POST['sym'];
        $diag = $_POST['diag'];
        $med = $_POST['med'];
        $dos = $_POST['dos'];
        $sdate = $_POST['sdate'];
        $edate = $_POST['edate'];
        $pp = $_POST['pp'];
		$numon = $_POST['nummonths'];
		$rd = $_POST['rd'];
		$fname = $_POST['fname'];
		$floc = $_POST['floc'];
        
        //Input Validations
        if (!isset($_POST['bp']) || $_POST['bp'] == '') {
                $errMsgArr[] = 'Blood Pressure missing';
                $errNum += 1;
        }
        if (!isset($_POST['weight']) || $_POST['weight'] == '') {
                $errMsgArr[] = 'Weight missing';
                $errNum += 1;
        }
        //test
        if (!isset($_POST['sym']) || $_POST['sym'] == '') {
                $errMsgArr[] = 'Symptoms missing';
                $errNum += 1;
        }
        if (!isset($_POST['diag']) || $_POST['diag'] == '') {
                $errMsgArr[] = 'Diagnosis address missing';
                $errNum += 1;
        }
        //end
        
        
        //$prepUsers = $db->prepare("SELECT * FROM `Users` WHERE UserName = :id ; ");     
		/*
        if ( $prepUsers->execute( array( ":id" => $user ) ) ) { 
                //IF NAME IS NOT IN USE
                if($prepUsers->rowCount() != 0){
                        $errMsgArr[] = 'Username already in use';
                        $errNum += 1;
                } 
        } else {
                $error = $prepUsers->errorInfo();
                $errMsgArr[] = $error[2];
                $errNum += 1;
                $retVal = outputXML($errNum, $errMsgArr, $db);          
        }
        */
        
        
        
        
        if($errNum == 0){
                        //set up and insert values into the user table
                        $updateVistPrep = $db->prepare("INSERT INTO Users(FirstName, LastName, UserName, Email, Birthday, SSN, Type, NeedApproval, Password) 
                                        VALUES(:fname, :lname, :login, :email, :bday, :ssn, :type, :needapproval, :password);");
                        //$tableType = '';
                        
                        //$needapproval;
                        //$type;

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
                                $errMsgArr[] = 'Insert into user table failed';
                                $errNum += 1;                           
                        }                       
                        else {
                        
                                //get the primary key for the recently entered row
                                $memIDPrep = $db->prepare("SELECT * FROM Users WHERE UserName = '" . $user . "'");
                                $getIDSuccess = $memIDPrep->execute();
                                if( ! $getIDSuccess ){
                                        $errMsgArr[] = 'Get user table ID failed';
                                        $errNum += 1;
                                } else {
                                        
                                        //add into the proper sub table with the user primary key as the member foreign key
                                        $member = $memIDPrep->fetch(PDO::FETCH_ASSOC);
                                        $insertTypePrep = $db->prepare("INSERT INTO " .$tableType. "(FK_member_id) VALUES('" .$member['PK_member_id']. "')");
                                        if( !($insertTypePrep->execute()) ){
                                                $errMsgArr[] =  "Insert into $tableType table failed";
                                                $errNum += 1;
                                        }
                                }
                        }
                        
                        $retVal = outputXML($errNum, $errMsgArr, $db);
                        
        } else {
                $retVal = outputXML($errNum, $errMsgArr, $db);
        }
        
                        
        return $retVal; 
        
}




?>
