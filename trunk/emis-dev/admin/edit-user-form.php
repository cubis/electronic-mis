<?php
//headers for session
session_start();
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
                $connection = @mysql_connect("devdb.fulgentcorp.com","495311team2user","680c12D5!gP592xViF") or die(mysql_error());
                $database = @mysql_select_db("cs49532011team2", $connection) or die(mysql_error());
                $table_name = "Users";
                $sql = "SELECT * FROM $table_name WHERE PK_member_id = '$_GET[ID]'";
                $result = @mysql_query($sql,$connection) or die(mysql_error());
                while ($row = mysql_fetch_array($result))
                {
 		    $f_name = $row['FirstName'];
                    $l_name = $row['LastName'];

                }
            ?>

            <table>
                <tr><td><h3>User's Personal Info</h3></td></tr>
                <tr>
                    <td>First Name:</td>
                    <td><input type="text" name="firstname" value= <?echo "$f_name";?> /></td>
                </tr>
                <tr>
                    <td>Last Name:</td><td>
                        <input type="text" name="lastname" value = <?echo "$l_name";?> /></td>
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
