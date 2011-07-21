<?php
	echo "<p>start</p>";
	if(isset($_GET['u']) and $_GET['u'] =='basil'){
	    echo "<p>user is: ".$_GET['u']."</p>";
        $to = "basilsattler@gmail.com";
        $subject = "Test mail from emis";
        $message = "Hello! This is a simple email message.\n no action required!";
        $from = "basilsattler@gmail.com";
        $headers = "From:" . $from;
        
        mail($to,$subject,$message,$headers);
        
        echo "<p>Mail Sent.</p>";
	}
	else
	    echo "<p>u= was not set correctly</p>\n";
	echo "<p>done...</p>";
	
?>
