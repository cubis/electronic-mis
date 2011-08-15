<?php
require_once('auth.php');
require_once('bootstrap.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Administrator Profile</title>
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
			<div class="logo"><a href="memberProfileView.php"><img src="../img/logo.png" /></a></div>
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
        <table>
            <tr>
              <td style="width: 200px;">First Name</td>
              <td style="width: 200px;">Last Name</td>
              <td style="width: 200px;">Username</td>
              <td style="width: 200px;">Edit</td>
            </tr>
			
<?php
global $currentPath;
$user = $_SESSION['SESS_USERNAME'];
$key = $_SESSION['SESS_AUTH_KEY'];
$request = $currentPath . "viewPatientREST.php?u=" 
			. urlencode($user) . "&key=" . urlencode($key) . "&pat=all";

//format and send request
$ch = curl_init($request);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);    
curl_setopt($ch, CURLOPT_TIMEOUT, 8);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch); //send URL Request to RESTServer... returns string
curl_close($ch); //string from server has been returned <XML> closethe channel

if( $output == ''){
	die("CONNECTION ERROR ");
}
//die($output);

$parser = xml_parser_create();
xml_parse_into_struct($parser, $output, $wsResponse, $wsIndices);
//print("OUTPUT = ".$output."\n");
//print("name = ".$wsResponse[$wsIndices['FIRSTNAME'][0]]['value']."\n");

$numrows = $wsResponse[$wsIndices['PATIENTCOUNT'][0]]['value'];
$currRow = 0;

while ($currRow < $numrows)
{
	$UserName = $wsResponse[$wsIndices['USERNAME'][$currRow]]['value'];
	if (!isset($wsResponse[$wsIndices['FIRSTNAME'][$currRow]]['value'])) {
		//print $currRow;
	}
	else
		$FirstName = $wsResponse[$wsIndices['FIRSTNAME'][$currRow]]['value'];
	if (!isset($wsResponse[$wsIndices['LASTNAME'][$currRow]]['value'])) {
		//print $currRow;
	}
	else
		$LastName = $wsResponse[$wsIndices['LASTNAME'][$currRow]]['value'];
	
	echo "<tr style=\"border-bottom: 1px solid black;\">\n";
	echo "<td style=\"padding: 15px 0px 15px 0px;\">",$FirstName,"</td>\n";
	echo "<td>",$LastName,"</td>\n";
	echo "<td>",$UserName,"</td>\n";
	echo "<td><a href='" . $currentPath . "editMemberView.php?u=$UserName'>Edit</a></td>\n";
	echo "</tr>\n";
	$currRow++;
}

?>
        </table>
        <br /><br />
        <a class="black_button" style="margin-right: 295px;"href='../memberProfileView.php'><span>Back</span></a>
        <br />
        <br />
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
