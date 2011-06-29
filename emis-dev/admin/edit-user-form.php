<?php
//headers for session
require_once('../auth.php');
require_once('../config.php');
require_once('../bootstrap.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Admin Editing</title>
    </head>
    <body>
        <h1>Admin User's Edit Form</h1>
            <?php
                $errflag = false;
                $qry = "SELECT * FROM Users WHERE UserName='{$_SESSION['SESS_USERNAME']}' AND Password='" . md5($_POST['SESS_PASSWORD']) . "'";
                $results = mysql_query($qry);
                $row = mysql_num_rows($result);
                echo "<td>",$row['FirstName'],"</td>\n";
            ?>

            <table>
                <tr><td><h3>User's Personal Info</h3></td></tr>
                <tr>
                    <td>First Name:</td>
                    <td><input type="text" name="firstname" /></td>
                </tr>
                <tr>
                    <td>Last Name:</td><td>
                        <input type="text" name="lastname" /></td>
                </tr>
                <tr>
                    <td>Sex:</td>
                    <td><select name="sex">
                            <option value = "m">Male</option>
                            <option value = "f">Female</option>
                        </select></td>
                </tr>
                <tr>
                    <td>Address:</td><td>
                        <input type="text" name="Address" /></td>
                </tr>
                <tr>
                    <td>Birthday("YYYY-MM-DD"):</td>
                    <td><input type="text" name="Birthday" /></td>
                </tr>
                <tr>
                    <td>SSN:</td><td>
                        <input type="text" name="SSN" /></td>
                </tr>
                <tr><td><hr><td></tr>
                <tr><td><h3>User's Contact Info </h3></tr><tr></tr>
                <tr>
                    <td>Email:</td>
                    <td><input type="text" name="email" /><br />
                </tr>
                <tr>
                    <td>Phone Number(###-###-####):</td>
                    <td><input type="text" name="pnumber" /></td>
                </tr>
                <tr>
                <tr><td><hr><td></tr>
                <tr><td><h3>User's Insurance Information</h3></td></tr>
                    <td>Insurance Policy Number:</td>
                    <td><input type="textbox" name="insurance" /></td>
                </tr>
                <tr>
                <tr><td><hr><td></tr>
                    <td><input type="submit" value="Submit" /></td><td></td>
                </tr>
            </table>
        </form>
    </body>
</html>
