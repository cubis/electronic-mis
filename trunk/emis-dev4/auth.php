<?php

//Start session
session_start();

/**************JUST MAKES SURE THE CLIENT SIDE DOESN'T SHOW SITES YOU CAN'T GET INFO FOR***********/


//Check whether the session variable SESS_MEMBER_ID is present or not
if ( !isset($_SESSION['SESS_MEMBER_ID']) || trim($_SESSION['SESS_MEMBER_ID']) == '' ) {
	die("SESS_ID: " . $_SESSION['SESS_MEMBER_ID'] . "SESS_USERNAME " . $_SESSION['SESS_USERNAME']);
    header("location: accessDeniedView.php");
    exit();
}

// Usage: #### (Patient Nurse Doctor Admin)
// ex: 0001 = Only Admin has Access
function restrictAccess ($access) {	
  session_start();
  $currentAccess = $_SESSION['SESS_TYPE'];
  if($currentAccess == 1) $i = 0;
  elseif($currentAccess == 200) $i = 1;
  elseif($currentAccess == 300) $i = 2;
  elseif($currentAccess == 400) $i = 3;
  else header("Location: memberProfileView.php");;
  if(!intval(substr($access, $i, 1))) header("Location: memberProfileView.php");
}
?>