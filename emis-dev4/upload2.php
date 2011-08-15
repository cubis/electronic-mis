<?php
/*
 * $savelocation is the location where your file will be stored.
 * The location is in relation to where upload.php is store, so
 * right now, it uploads to a folder called upload. It is not
 * created by default, so make sure this folder exists on your
 * host machine! Also, make sure the file permission for the
 * upload location allows everyone to write to it!
 */


require_once('configREST.php');     //sql connection information
require_once('bootstrapREST.php'); 

 //$savelocation = "upload/";
 //edit this to indicate where you want to save file
 $savelocation = "upload/" . basename( $_FILES['uploaded']['name']) ; 
 $ok=1;
 
 $filetype = $_FILES['uploaded']['type'];
 $filesize = $_FILES['uploaded']['size'];
 
 $fname = $_FILES['uploaded']['name'];

 
 if($filesize > 3000000) 
 { 
    echo "Your file is larger than 3 MB!<br />"; 
    $ok=0; 
 } 
 if(($filetype != "image/jpeg") && ($filetype != "image/pjpeg"))
 {
     echo "You need to upload a jpeg file!<br />";
     echo "Uploaded filetype: " . $uploaded_type . "<br />";
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
 
 $content = file_get_contents($savelocation);

$aid = $_GET['aid'];

$qry = $db->prepare('INSERT INTO Files (Name, Size, Type, Content, FK_ApptID) VALUES (?, ?, ?, ?, ?)');
$qryStatus = $qry->execute(array($fname, $filesize, $filetype, $content, $aid));
 

?>
