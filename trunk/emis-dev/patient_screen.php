<?php
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Viewing Information</title>
        <link href="css/styles.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <center><h1 style="color: white; margin-top: 50px;">Viewing <?echo "{$_SESSION['SESS_USERNAME']}";?> Information</h1></center>
            <div style="width: 400px; margin-left: auto; margin-right: auto;">
            <div class="login_box">
                <center>
                    <img src="img/logo.png" alt="Electronic Medical Information System">
                </center>
                <div>
                    <script type="text/javascript">
                        function submitform()
                        {
                        document.forms["loginForm"].submit();
                        }
                    </script>
            <?php
                $connection = @mysql_connect("devdb.fulgentcorp.com","495311team2user","680c12D5!gP592xViF") or die(mysql_error());
                $database = @mysql_select_db("cs49532011team2", $connection) or die(mysql_error());
                $table_name = "Users";
                $sql = "SELECT * FROM $table_name WHERE UserName = '{$_SESSION['SESS_USERNAME']}'"; 
                $result = @mysql_query($sql,$connection) or die(mysql_error());
                while ($row = mysql_fetch_array($result))
                {
                    $user = $row['UserName'];
                    $f_name = $row['FirstName'];
                    $l_name = $row['LastName'];
                    $sex = $row['Sex'];
                    $email = $row['Email'];
                    $birthday = $row['Birthday'];
                    $phone = $row['PhoneNumber'];
                    $ssn = $row['SSN'];
//                  $address = $row['Address'];
//                  $policy = $row['Policy'];


                }
           ?>
            <center><table>
                <tr>
                    <td><h3><?echo "$user Personal Infomation";?></h3></td>
                </tr>
                <tr>
                    <td>First Name:</td>
                    <td><b><?echo "$f_name";?><b></td>
                </tr>
                <tr>
                    <td>Last Name:</td>
                    <td><b><?echo "$l_name";?><b></td>
                </tr>
                <tr>
                    <td>Sex:</td>
                    <td><b><?echo "$sex";?><b></td>
                </tr>
                <tr>
                    <td>Address:</td>
                    <td><b><?echo "$address";?><b></td>                
                </tr>
                <tr>
                    <td>Birthday("YYYY-MM-DD"):</td>
                    <td><b><?echo "$birthday";?><b></td>
                </tr>
                <tr>
                    <td>SSN:</td>
                    <td><b><?echo "$ssn";?><b></td>
                </tr>
                <tr>
                    <td><h3><div class="dashed_line"></div><?echo "$user Contact Information";?></h3></tr><tr>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td><b><?echo "$email";?><b></td>
                </tr>
                <tr>
                    <td>Phone Number(###-###-####):</td>
                    <td><b><?echo "$phone";?><b></td>
                </tr>
                <tr>
                    <td><h3><div class="dashed_line"></div><? echo "$user Insurance Information";?></h3></td>
                </tr>
                <tr>
                    <td>Insurance ID:</td>
                    <td><b><?echo "$policy";?><b></td>
                </tr>
                <tr>
                    <td>Insurance Group:</td>
                    <td><b><?echo "$policy";?><b></td>
                </tr>
                <tr>
                    <td>Co-Pay:</td>
                    <td><b><?echo "$policy";?><b></td>
                </tr>
                <tr>
                    <td>Coverage Start:</td>
                    <td><b><?echo "$policy";?><b></td>
                </tr>
                <tr>
                    <td>Coverage Ends:</td>
                    <td><b><?echo "$policy";?><b></td>
                </tr>
                    <td><h3><div class="dashed_line"></div><? echo "$user Medical Information";?></h3></td>
                </tr>
                <tr>    
                    <td>Allergies:</td>
                    <td><b><?echo "$policy";?><b></td>
                </tr>
                <tr>
                    <td>Medication:</td>
                    <td><b><?echo "$policy";?><b></td>
                </tr>
                <tr>
                    <td>Primary Doctor:</td>
                    <td><b><?echo "$policy";?><b></td>
                </tr>
                <tr>
                    <td><div class="dashed_line"></div>
                </tr>
            </table></center>
          <a class="black_button" style="margin-right: 170px;" href="member-profile.php"><span>Back</span></a>

    </body>
</html>
