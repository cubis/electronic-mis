<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Make Appointment</title>
    </head>
    <body>
        
        <?php
        /*require_once('/configREST.php');
        require_once('/bootstrapREST.php');
        require_once('/authenticateREST.php');*/
        ?>
        
        <h1>Make Appointment</h1>
        <br />
        
        <form action="make_appt.php" method="post">
        
        <?php
        echo 'Doctor:
            <select name="doctor">
            <option value="1">Select Doctor</option>
            </select>
        
        <br />
        <br />
        
        Date: ';
                
                
                
                $days = range(1,31);
                $years = range(2011,2061);
                $hours = range(0,23);
                
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
                foreach($days as $value)
                {
                    echo "<option value=\"$value\">$value </option>\n";
                }
                echo '</select>';
                
                //Dropdown box for years
                echo '<select name="year">';
                foreach($years as $value)
                {
                    echo "<option value=\"$value\">$value </option>\n";
                }
                echo '</select>';
                
                echo '<br /><br />';
                echo 'Time:';
                
                //Dropdown box for hours
                echo '<select name="hour">';
                foreach($hours as $value)
                {
                    echo "<option value=\"$value\">$value </option>\n";
                }
                echo '</select>';
                
                
                ?>
                
            
            <p>
                Reason for visit:
                <br />
            <!-- Textbox -->
                <textarea name="reason" cols="40" rows="5">Please limit your response to 2000 characters.</textarea>
            </p>
            
            <p>
                <!-- Whether user wants reminders before his appointment -->
                Would you like reminders sent prior to your appointment?
                <br />
                <input type="radio" name="reminder" value="true" checked="checked" /> Yes<br />
                <input type="radio" name="reminder" value="false" /> No
            </p>
            
            <input type="submit" value="Submit" />
            
        </form>

        
        <?php
        // put your code here
        ?>
    </body>
</html>
