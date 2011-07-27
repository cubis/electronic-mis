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
?>