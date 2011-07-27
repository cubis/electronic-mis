<?php
require_once('../auth.php');
require_once('../configREST.php');
require_once('../bootstrapREST.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Administrator Profile</title>
        <link href="../css/styles.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <center><h1 style="color: white; margin-top: 50px;">Admin Editing</h1></center>
            <div style="width: 630px; margin-left: auto; margin-right: auto;">
                <center>
                    <img src="../img/logo.png" alt="Electronic Medical Information System">
                </center>
                <div>
                    <script type="text/javascript">
                        function submitform()
                        {
                        document.forms["loginForm"].submit();
                        }
                    </script>
        </center>
        <br>
        <table border="1">
            <tr>
              <td>First Name</td>
              <td>Last Name</td>
              <td>Username</td>
              <td>Edit</td>
            </tr>
			
<?php
$user = $_SESSION['SESS_USERNAME'];
$key = $_SESSION['SESS_AUTH_KEY'];
$request = "http://localhost/emis/emis-dev3/viewPatientREST.php?u=" 
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
	
	echo "<tr>\n";
	echo "<td>",$FirstName,"</td>\n";
	echo "<td>",$LastName,"</td>\n";
	echo "<td>",$UserName,"</td>\n";
	echo "<td><a href='http://localhost/emis/emis-dev3/editMemberView.php?u=$UserName'>Edit</a></td>\n";
	echo "</tr>\n";
	$currRow++;
}

?>
        </table>
        <a class="black_button" style="margin-right: 295px;"href='../memberProfileView.php'><span>Back</span></a>
     </body>
</html>
