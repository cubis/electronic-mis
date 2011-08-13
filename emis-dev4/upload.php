<?php
/*
 * $savelocation is the location where your file will be stored.
 * The location is in relation to where upload.php is store, so
 * right now, it uploads to a folder called upload. It is not
 * created by default, so make sure this folder exists on your
 * host machine! Also, make sure the file permission for the
 * upload location allows everyone to write to it!
 */

 //$savelocation = "upload/";
 //edit this to indicate where you want to save file
 $savelocation = "upload/" . basename( $_FILES['uploaded']['name']) ; 
 $ok=1;
  
 if($uploaded_size > 3000000) 
 { 
    echo "Your file is larger than 3 MB!<br />"; 
    $ok=0; 
 } 
 if(($uploaded_type !="image/jpeg") || $uploaded_type !="image/jpg")
 {
     echo "You need to upload a jpeg file!<br />";
     $ok=0;
 }
 if($uploaded_type == "text/php")
 { 
    echo "You tried uploading a PHP file! You sneak!<br />"; 
    $ok=0; 
 } 
 
 
 if($ok==0)
 {
     echo "You tried uploading a file of improper format. Make sure it is of type jpeg and less than 3MB in size.<br />";
 }
 else //uploads file to the directory you indicated
 {
     if(move_uploaded_file($_FILES['uploaded']['tmp_name'], $savelocation)) 
    {
        echo "The file ". basename( $_FILES['uploadedfile']['name']). " has been uploaded";
    } 
    else
    {
        echo "Haha! File upload failed! You're a loser! <br />-Courtesy of elite2048";
    }
 }
 
 
 

?>
