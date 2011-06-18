<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Change Password</title>
    </head>
    <body>
        
        <h1>Change Password</h1>
        
        <form action="change_pass.php" method="post">
            <p>
                Current Password: <input type="password" name="oldpass" size="10" maxlength="20" />
            </p>
            
            <p>
                New Password: <input type="password" name="newpass1" size="10" maxlength="20" />
            </p>
            
            <p>
                Confirm New Password: <input type="password" name="newpass2" size="10" maxlength="20" />
            </p>
            
            <p>
                <input type="submit" name="Submit" value="Change Password" />
            </p>
            
        </form>
        
        <?php
        
        $old_pass = $_POST['oldpass'];
        
        $new_pass1 = $_POST['newpass1'];
        
        $new_pass2 = $_POST['newpass2'];
        
        echo "<p>Old Password: $old_pass</p>";
        
        echo "<p>New Password1: $new_pass1</p>";
        
        echo "<p>New Password2: $new_pass2</p>";
        
        $qry="SELECT * FROM Users WHERE UserName= {$SESSION['SESS_USERNAME']} AND Password='".md5($_POST['old_pass'])."'";
        $result=mysql_query($qry);
	
	//Check whether the query was successful or not
	if($result) {
		if(mysql_num_rows($result) == 1) {
			//Login Successful
			session_regenerate_id();
			$member = mysql_fetch_assoc($result);
			$_SESSION['SESS_MEMBER_ID'] = $member['PK_member_id'];
			$_SESSION['SESS_FIRST_NAME'] = $member['FirstName'];
			$_SESSION['SESS_LAST_NAME'] = $member['LastName'];
			$_SESSION['SESS_TYPE'] = $member['Type'];
			$_SESSION['SESS_USERNAME'] = $member['UserName'];
			session_write_close();
			header("location: member-profile.php");
			exit();
		}else {
			//Login failed
			header("location: login-failed.php");
			exit();
        
        
        
        
        
        ?>
    </body>
</html>
