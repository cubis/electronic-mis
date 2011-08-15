<?php
session_start();
?>
<?php
if (isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) > 0) {
    echo '<ul class="err">';
    foreach ($_SESSION['ERRMSG_ARR'] as $msg) {
        echo '<li>', $msg, '</li>';
    }
    echo '</ul>';
    unset($_SESSION['ERRMSG_ARR']);
}
?>
<?php
require_once('auth.php');
//require_once('configREST.php');     //sql connection information
require_once('bootstrap.php');  //link information
//$appid = $_GET['ID'];
//$docListPrep = $db->prepare("SELECT * FROM Doctor");
//$doclist = "SELECT * FROM Doctor;";
//$result = mysql_query($doclist) or die(mysql_error());
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Electronic Medical Information System (EMIS) - Visit</title>
        <link href="css/logged_in_styles.css" rel="stylesheet" type="text/css" />
    </head>

<body>
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
                    <script type="text/javascript">
                        function submitform()
                        {
                            document.forms["visit"].submit();
                        }
                    </script>
                    <?php
                    $apptid = $_GET['ID'];
                    echo '<form id="visitForm" name="visitForm" enctype="multipart/form-data" method="post" action="visitExec.php?ID='.$apptid.'">';
                    ?>
                    <!--<form id="visitForm" name="visitForm" method="post" action="visitExec.php?ID=<?php //echo $_GET[$ID] ?>">-->
                        <center><p><b>Fill in the visit information below.</b></p></center>
                        <?php
                        //echo "<p>ID=".$apptid."</p>";
                        ?>
                        <div class="dashed_line"></div>
                        <table width="300" border="0" align="center" cellpadding="2" cellspacing="0">
                            <tr>
                                <th>Blood Pressure:</th>
                                <td><input name="bp" type="text" class="textfield" id="bp" /></td>
                            </tr>

                            <tr>
                                <th>Weight</th>
                                <td><input name="weight" type="text" class="textfield" id="weight" /></td>
                            </tr>

                            <tr>
                                <th>Symptoms</th>
                                <td><input name="sym" type="text" class="textfield" id="sym" /></td>
                            </tr>

                            <tr>
                                <th>Diagnosis</th>
                                <td><input name="diag" type="text" class="textfield" id="diag" /></td>
                            </tr>

                            <tr>
                                <th>Medicine (If Needed)</th>
                                <td><input name="med" type="text" class="textfield" id="med" /></td>
                            </tr>

                            <tr>
                                <th>Dosage</th>
                                <td><input name="dos" type="text" class="textfield" id="dos" /></td>
                            </tr>

                            <tr>
                                <th>Start Date (MMDDYYYY):</th>
                                <td><input name="sdate" type="text" class="textfield" id="sdate" /></td>
                            </tr>

                            <tr>
                                <th>End Date (MMDDYYYY):</th>
                                <td><input name="edate" type="text" class="textfield" id="edate" /></td>
                            </tr>

                            <tr>
                                <th>Total Bill:</th>
                                <td><input name="bill" type="text" class="textfield" id="bill" /></td>
                            </tr>

                            <tr>
                                <th>Payment Plan:</th>
                                <td><input name="pp" type="text" class="textfield" id="pp" /></td>
                            </tr>

                            <tr>
                                <th>Number of Months:</th>
                                <td><input name="nummonths" type="text" class="textfield" id="nummonths" /></td>
                            </tr>


                            <tr>
                                <th>Referal Docotor</th>
                                <!--<select name ="rd" id="rd">
                                    <option value =""></option>-->
                                <?php
                                //$appt = $docListPrep->execute();
                                // while ($row1 = $appt->fetch(PDO::FETCH_ASSOC)){
                                //      echo '<option value = "', $row1['PK_DoctorID'], '">', $row1['DocName'], '</option>';
                                //}
                                ?>
                                </select>

                                <td><input name="rd" type="text" class="textfield" id="rd" /></td>
                            </tr>

                            <tr>
                                <th>File Name</th>
                                <td><input name="fname" type="text" class="textfield" id="fname" /></td>
                            </tr>

                            <tr>
                                <th>File location</th>
                                <td><input name="floc" type="text" class="textfield" id="floc" /></td>
                            </tr>
                            
							<tr>
								<th>File To Upload</th>
								<th><input name="uploadfile" type="file" id="uploadfile" /></th>
                            

                            <tr>
                                <td>&nbsp;</td>
                                <td><input type="submit" /> </td>
<!--<td><a class="black_button" href="javascript: submitform()"><span>Submit</span></a></td>-->
                            </tr>
                        </table>
                    </form>
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