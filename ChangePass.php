<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Change Password</title>
    </head>
    <body>
        
        <h1>Change Password</h1>
        
        <form action="password.php" method="post">
            <p>
                Current Password: <input type="password" name="pass" size="10" maxlength="20" />
            </p>
            
            <p>
                New Password: <input type="password" name="pass1" size="10" maxlength="20" />
            </p>
            
            <p>
                Confirm New Password: <input type="password" name="pass2" size="10" maxlength="20" />
            </p>
            
            <p>
                <input type="submit" name="Submit" value="Change Password" />
            </p>
            
        </form>
        
        <?php
        // put your code here
        ?>
    </body>
</html>
