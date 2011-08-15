<?php
require_once('configREST.php');     //sql connection information
require_once('bootstrapREST.php');  //link information

doService();

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
        return outputXML($errNum, $errMsgArr, '');
    }


    //USE CREDENTIALS AND AUTHENTICATE
    
    $user = $_GET['u'];
    $recKey = $_GET['key'];
	$aid = $_GET['aid'];
    $userInfoPrep = $db->prepare("SELECT * FROM Users WHERE UserName = :user;");
    $userInfoSuccess = $userInfoPrep->execute(array(":user" => $user));
    $memberInfo = $userInfoPrep->fetch(PDO::FETCH_ASSOC);
    //failed to access database for user info
    if (!$userInfoSuccess) {
        $errMsgArr[] = "DATABASE ERROR ONE";
        $errNum++;
        return outputXML($errNum, $errMsgArr, '');
    }
    $currKey = $memberInfo['CurrentKey'];
    $trustString = "xolJXj25jlk56LJkk5677LS";
    $trustedKey = md5($currKey . $trustString);


    if ($recKey == $trustedKey || $recKey == $currKey) {
	

	$userInfoPrep = $db->prepare("SELECT fileLocation FROM Appointment WHERE PK_AppID = :aid;");
    $userInfoSuccess = $userInfoPrep->execute(array(":aid" => $aid));
    $memberInfo = $userInfoPrep->fetch(PDO::FETCH_ASSOC);
    //failed to access database for user info
    if (!$userInfoSuccess) {
        $errMsgArr[] = "DATABASE ERROR ONE";
        $errNum++;
        return outputXML($errNum, $errMsgArr, '');
    }

	

	//TEST STUFF
//CHANGE THIS TO SUIT YOUR APP
$file_size = $memberInfo['fileSize']; //bytes
$path_to_file = $memberInfo['fileLocation'];
$path_to_file = rawurldecode($path_to_file);

//SEND HEADER
//@ob_end_clean();
//@ini_set('zlib.output_compression', 'Off');
//header('Pragma: public');
//header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT'); 
//header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1 
//header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1 
header("Content-type: image/jpeg");
//header('Content-Disposition: inline; filename="' . basename($path_to_file) . '"');
//header("Content-length: $file_size");

//SEND FILE DATA
/*$file = fopen($path_to_file, "rb");
if ($file) {
  while(!feof($file)) {
    print(fread($file, 8192));
    flush();
    if (connection_status() != 0) {
      fclose($file);
      die();
    }
  }
  fclose($file);
}*/

$content = file_get_contents($path_to_file = rawurldecode($path_to_file));
print($content);

		
    } else {
        die("Unauthorized to view information");
        
    }
   
 //  $retVal = "STUFF";
   
   return;
}
