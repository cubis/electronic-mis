<?php
require_once("auth.php");
require_once('bootstrap.php');
$userName = $_SESSION['SESS_USERNAME'];

global $currentPath;
$request = $currentPath . "viewPatientREST.php?";
$request .= "u=" . urlencode($userName);
$request .= "&key=" . urlencode($_SESSION['SESS_AUTH_KEY']);
$request .= "&pat=" . urlencode($_GET['u']);

//die($request);
//format and send request
$ch = curl_init($request);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);    
curl_setopt($ch, CURLOPT_TIMEOUT, 8);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$RESToutput = curl_exec($ch); //send URL Request to RESTServer... returns string
curl_close($ch); //string from server has been returned <XML> closethe channel

if( $RESToutput == ''){
	die("CONNECTION ERROR");
}
//die($RESToutput);
//parse return string
$parser = xml_parser_create();	
xml_parse_into_struct($parser, $RESToutput, $wsResponse, $wsIndices);

$errNum = $wsResponse[$wsIndices['ERRNUM'][0]]['value'];

/*print( "OUTPUT|$RESToutput|\n");
print "<pre>";
print_r($wsResponse);
print_r($wsIndices);
print "</pre>";*/

if ($errNum != 0) {
	die("FUCK!" . $wsResponse[$wsIndices['ERROR'][0]]['value']);
}

$FirstName = $wsResponse[$wsIndices['FIRSTNAME'][0]]['value'];
$LastName = $wsResponse[$wsIndices['LASTNAME'][0]]['value'];
$Sex = $wsResponse[$wsIndices['SEX'][0]]['value'];
$Birthday = $wsResponse[$wsIndices['BIRTHDAY'][0]]['value'];
$SSN = $wsResponse[$wsIndices['SSN'][0]]['value'];
$Email = $wsResponse[$wsIndices['EMAIL'][0]]['value'];
$PhoneNumber = $wsResponse[$wsIndices['PHONENUMBER'][0]]['value'];
$CompanyName = $wsResponse[$wsIndices['COMPANYNAME'][0]]['value'];
$PlanType = $wsResponse[$wsIndices['PLANTYPE'][0]]['value'];
$PlanNum = $wsResponse[$wsIndices['PLANNUM'][0]]['value'];
$CoveragePercent = $wsResponse[$wsIndices['COVERAGEPERCENT'][0]]['value'];
$CoPay = $wsResponse[$wsIndices['COPAY'][0]]['value'];
$CoverageStart = $wsResponse[$wsIndices['COVERAGESTART'][0]]['value'];
$CoverageEnd = $wsResponse[$wsIndices['COVERAGEEND'][0]]['value'];
$Locked = $wsResponse[$wsIndices['LOCKED'][0]]['value'];
$NeedApproval = $wsResponse[$wsIndices['NEEDAPPROVAL'][0]]['value'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Viewing Information</title>
	<link href="css/logged_in_styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
	<script type="text/javascript">
		function submitform()
		{
			document.forms["editMemberForm"].submit();
		}
	</script>
    <div class="container">
        <div class="header">
            <div class="logo"><a href="memberProfileView.php"><img src="img/logo.png" /></a></div>
            <div class="welcome_text">
                <h1>Welcome,
                <?php
                    echo $_SESSION['SESS_FIRST_NAME']; 
                ?></h1>
            </div>
        </div>
        <div class="contentwrap">
            <div class="navigation">
                <div class="nav_content">
					<?php
                    	include_once "generateNav.php"; // This will generate a navigation menu according to the user's role.
					?>
                </div>
            </div>
            <div class="page_display">
                <div class="page_title">Edit Member Information</div>
                <div class="page_content">
                <!-- PAGE CONTENT STARTS HERE -->
                    <form name="editMemberForm" method="post" action="editMemberExec.php">
                        <input type="hidden" name="UserName" value="<?php echo $_GET['u']; ?>" />
                        <input type="hidden" name="AuthKey" value="<?php echo $_SESSION['SESS_AUTH_KEY']; ?>" />
                        <input type="hidden" name="CallingUserName" value="<?php echo $_SESSION['SESS_USERNAME']; ?>" />
						<input type="hidden" name="PersonalID" value="<?php echo $_SESSION['SESS_PERSONAL_ID']; ?>" />
						<input type="hidden" name="MemberID" value="<?php echo $_SESSION['SESS_MEMBER_ID']; ?>" />
                        
                        <table>
							<tr>
								<td><h3>Personal Infomation</h3></td>
							</tr>
                            <tr>
                                <td>First Name:</td>
                                <td>
                                    <input type="text" name="FirstName" value="<?php echo "$FirstName" ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td>Last Name:</td>
                                <td><input type="text" name="LastName" value="<?php echo "$LastName" ?>" /></td>
                            </tr>
                            <tr>
                                <td>Sex:</td>
                                <td><input type="text" name="Sex" value="<?php echo "$Sex" ?>" /></td>
                            </tr>
                            <tr>
                                <td>Birthday ("YYYY-MM-DD"):</td>
                                <td><input type="text" name="Birthday" value="<?php echo "$Birthday" ?>" /></td>
                            </tr>
                            <tr>
                                <td>SSN:</td>
                                <td><input type="text" name="SSN" value="<?php echo "$SSN" ?>" /></td>
                            </tr>
                            <tr>
                                <td><h3><div class="dashed_line"></div>Contact Information</h3></tr><tr>
                            </tr>
                            <tr>
                                <td>Email:</td>
                                <td><input type="text" name="Email" value="<?php echo "$Email" ?>" /></td>
                            </tr>
                            <tr>
                                <td>Phone Number (###-###-####):</td>
                                <td><input type="text" name="PhoneNumber" value="<?php echo "$PhoneNumber" ?>" /></td>
                            </tr>
                            <tr>
                                <td><h3><div class="dashed_line"></div>Insurance Information</h3></td>
                            </tr>
                            <tr>
                                <td>Insurance Group:</td>
                                <td><input type="text" name="Company_Name" value="<?php echo "$CompanyName" ?>" /></td>
                            </tr>
                            <tr>
                                <td>Plan Type:</td>
                                <td><input type="text" name="Plan_Type" value="<?php echo "$PlanType" ?>" /></td>
                            </tr>
                            <tr>
                                <td>Plan Number:</td>
                                <td><input type="text" name="Plan_Num" value="<?php echo "$PlanNum" ?>" /></td>
                            </tr>
                            <tr>
                                <td>Co-Pay:</td>
                                <td><input type="text" name="Co-Pay" value="<?php echo "$CoPay" ?>" /></td>
                            </tr>
                            <tr>
                                <td>Coverage Start:</td>
                                <td><input type="text" name="Coverage-Start" value="<?php echo "$CoverageStart" ?>" /></td>
                            </tr>
                            <tr>
                                <td>Coverage Ends:</td>
                                <td><input type="text" name="Coverage-End" value="<?php echo "$CoverageEnd" ?>" /></td>
                            </tr>
                             <?php if ($_SESSION['SESS_TYPE'] >= 400): // Logged in user is waiting approval ?>
                     <tr>
								<td><h3>Account Status</h3></td>
							</tr>
					 <tr>
                        <td>Unlocked:</td>
                        <td>
                        <input type="radio" name="Status" value="unlock"
                        <?php if(!$Locked) echo "checked"; ?>  />
                        </td>
                     </tr>
                     <tr>
                        <td>Locked:</td>
                        <td>
                        <input type="radio" name="Status" value="lock"
                        <?php if($Locked) echo "checked"; ?>  />
                        </td>
                     </tr>
                     <?php endif; ?>

                            </table>
							<br /><br />
							<a class="black_button" style="margin-right: 60px;" href="memberProfileView.php"><span>Cancel</span></a>
                            <a class="black_button" style="margin-right: 60px;" href="javascript: submitform()"><span>Save Changes</span></a>
						</form>
                        <br /><br />
					<!-- END OF PAGE CONTENT -->
                </div>
            </div>
        </div>
        <div class="footer">
        	<p>Electronic Medical Information System. Copyright &copy; 2011 Team B. The University of Texas at San Antonio.</p>
        </div>
	</div>
</body>
</html>
