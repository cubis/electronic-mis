<html>
    <body>
        <h1>Admin User's Edit Form</h1>
        <!-- Modify to new directory to post -->
        <form action ="/create_user_submit.php" method ="POST">
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
                    <td><input type="textbox" name="insurance" /></td><!--may be modified later depending on how we define insurance-->
                </tr>
                <tr>
                <tr><td><hr><td></tr>
                    <td><input type="submit" value="Submit" /></td><td></td>
                </tr>
            </table>
        </form>

    </body>
</html>
