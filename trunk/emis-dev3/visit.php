<?php
require_once('auth.php');
require_once('configREST.php');     //sql connection information
require_once('bootstrap.php');  //link information
$appid = $_GET['ID'];

$doclist = "SELECT * FROM Doctor;";
$result = mysql_query($doclist) or die(mysql_error());
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Electronic Medical Information System (EMIS) - Visit</title>
        <link href="css/styles.css" rel="stylesheet" type="text/css" />
    </head>

    <body bgcolor="#aaaaff">
        <center><h1 style="color: white; margin-top: 50px;">Visit</h1></center>
        <div style="width: 400px; margin-left: auto; margin-right: auto;">
            <div class="login_box">
                <center>
                    <img src="img/logo.png" alt="Electronic Medical Information System" />
                </center>
                <div>
                    <script type="text/javascript">
                        function submitform()
                        {
                            document.forms["visit"].submit();
                        }
                    </script>
                    <form id="visitForm" name="visitForm" method="post" action="visitExec.php?ID=<?php echo $apptid ?>">
                        <center><p><b>Fill in the visit information below.</b></p></center>
                        <?php
                        ?>
                        <div class="dashed_line"></div>
                        <table width="300" border="0" align="center" cellpadding="2" cellspacing="0">
                            <tr>
                                <th>Blood Preasure:</th>
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
                                <select name ="rd" id="rd">
                                    <option value =""></option>
                                    <?php
                                    while ($row1 = mysql_fetch_assoc($result)) {
                                        echo '<option value = "', $row1['PK_DoctorID'], '">', $row1['DocName'], '</option>';
                                    }
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
                                <td>&nbsp;</td>
                                <td><input type="submit" /> </td>
<!--<td><a class="black_button" href="javascript: submitform()"><span>Submit</span></a></td>-->
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
