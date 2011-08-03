
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
require_once('configREST.php');
require_once('bootstrapREST.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Electronic Medical Information System</title>
    <link href="css/logged_in_styles.css" rel="stylesheet" type="text/css" />
</head>
    <body>
	<script type="text/javascript">
		function submitform()
		{
			document.forms["loginForm"].submit();
		}
	</script>
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
                <div class="page_title">Make Co-Payment</div>
                <div class="page_content">
                <!-- PAGE CONTENT STARTS HERE -->
<!-- Include the appoint id in copay-->
        <form action="coPayExec.php" method="post">

            <?php
            echo "Amount: ";
            echo "<input name='amount' type ='text' class='textfield' id='amount'/><br/>";

            echo "<br />";
            echo "<br />";

            echo 'Date: ';

            $days = range(1, 31);
            $years = range(2011, 2061);
            $hours = range(0, 23);

            //Dropdown box for months
            echo '<select name="month">
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
            </select>';

            //Dropdown box for days
            echo '<select name="day">';
            foreach ($days as $value) {
                echo "<option value=\"$value\">$value </option>\n";
            }
            echo '</select>';

            //Dropdown box for years
            echo '<select name="year">';
            foreach ($years as $value) {
                echo "<option value=\"$value\">$value </option>\n";
            }
            echo '</select>';

            echo '<br /><br />';
            ?>

            <br />

            

            <input type="submit" value="Submit" />

        </form>


<?php


?>
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
