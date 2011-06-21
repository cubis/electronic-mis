<html>
    <body>
        <h1>Create User Form</h1>
        <!-- Modify to new directory to post -->
        <form action ="/create_user_submit.php" method ="POST">
            <table>
                <tr><td><h3>Personal Info</h3></td></tr>
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
                    <td>Birthday("YYYY-MM-DD"):</td>
                    <td><input type="text" name="Birthday" /></td>
                </tr>
                <tr>
                    <td>SSN:</td><td>
                        <input type="text" name="SSN" /></td>
                </tr>
                <tr><td><hr><td></tr>
                <tr><td><h3>Contact Info </h3></tr><tr></tr>
                <tr>
                    <td>Email:</td>
                    <td><input type="text" name="email" /><br />
                </tr>
                <tr>
                    <td>Phone Number(##########):</td>
                    <td><input type="text" name="pnumber" /></td>
                </tr>
                <tr><td><hr><td></tr>
                <tr><td><h3>Account Info </h3></tr><tr></tr>
                <tr>
                    <td>Desired Account:</td>
                    <td> <select name="accttype">
                            <option value ="1">Patient</option>
                            <option value ="0">Doctor</option>
                            <option value ="0">Nurse</option>
                            <option value ="0">Admin</option>
                        </select></td>
                </tr>
                <tr>
                    <td>Desired Username:</td>
                    <td><input type="text" name="uname" /></td>
                </tr>
                <tr>
                    <td>Desired Password:</td>
                    <td><input type="text" name="pass" /></td>
                </tr>
                <tr>
                    <td>Confirm Password:</td>
                    <td><input type="text" name="pass2" /></td>
                </tr>
                <tr>
                    <td>Insurance Information:</td>
                    <td><input type="textbox" name="insurance" /></td><!--may be modified later depending on how we define insurance-->
                </tr>
                <tr>
                    <td><input type="submit" value="Submit" /></td><td></td>
                </tr>
            </table>
        </form>

    </body>
</html>