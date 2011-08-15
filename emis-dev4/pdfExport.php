<?php
require_once('auth.php');
require_once('bootstrap.php');
restrictAccess('1000');

$userName = $_SESSION['SESS_USERNAME'];

global $currentPath;
$request = $currentPath . "apptViewREST.php?";
$request .= "u=" . urlencode($userName);
$request .= "&key=" . urlencode($_SESSION['SESS_AUTH_KEY']);

//format and send request
$ch = curl_init($request);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);    
curl_setopt($ch, CURLOPT_TIMEOUT, 8);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$RESToutput = curl_exec($ch); //send URL Request to RESTServer... returns string
curl_close($ch); //string from server has been returned <XML> closethe channel



if( $RESToutput == ''){
  die("CONNECTION ERROR");
}

//parse return string
$parser = xml_parser_create();	
xml_parse_into_struct($parser, $RESToutput, $wsResponse, $wsIndices);
xml_parser_free($p);

$errNum = $wsResponse[$wsIndices['ERRNUM'][0]]['value'];
if ($errNum != 0) {
  $ct = 0;
  while($ct < $errNum){
    $err_msg_arr[] = $wsResponse[$wsIndices['ERROR'][$ct]]['value'];
    $ct++;
  }
  $_SESSION['ERRMSG_ARR'] = $err_msg_arr;
}
$numRows = $wsResponse[$wsIndices['APPTCOUNT'][0]]['value'];
$appointments = array();
for($x = 0 ; $x < $numRows; $x++) { // For each appointment, add to $appointments
  $aid = $wsResponse[$wsIndices['APPTID'][$x]]['value'];
  $adoctor = $wsResponse[$wsIndices['DOCNAME'][$x]]['value'];
  $atime = $wsResponse[$wsIndices['REASON'][$x]]['value'];
  $adate = $wsResponse[$wsIndices['DATE'][$x]]['value'];
  $areason = $wsResponse[$wsIndices['TIME'][$x]]['value'];
  $aremind = $wsResponse[$wsIndices['REMIND'][$x]]['value'];
  $status = $wsResponse[$wsIndices['STATUS'][$x]]['value'];
  $patient = $wsResponse[$wsIndices['PATLASTNAME'][$x]]['value'];
  $appointments[$x] = array($aid, $adate, $atime, $adoctor, $areason, $aremind, $status, $patient);
}

require('fpdf17/fpdf.php');

class PDF extends FPDF
{
  // Page header
  function Header()
  {
    // Logo
    $this->Image('img/horizontal_logo.png',10,6,50);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Move to the right
    $this->Cell(70);
    // Title
    $this->Cell(52,10,'Appointment Receipt',0,0,'C');
    // Line break
    $this->Ln(20);
  }

  // Page footer
  function Footer()
  {
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Page number
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
  }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);

foreach($appointments as &$app) {
  if($app[0] = $_GET['aid']) {
    //appoint array  array($aid, $adate, $atime, $adoctor, $areason, $aremind, $status, $patient)
    
    //date
    $pdf->Cell(18,10,'Date:',0);
    $date = DateTime::createFromFormat('Y-m-d', $app[1]);
    $pdf->Cell(40,10,$date->format('D, M dS, Y'),0);
    $pdf->Ln();
    //time
    $pdf->Cell(18,10,'Time:',0);
    $date = DateTime::createFromFormat('H:i:s', $app[4]);
    $pdf->Cell(40,10,$date->format('H:ia'),0);
    $pdf->Ln();
    //status
    $pdf->Cell(18,10,'Status:',0);
    $pdf->Cell(40,10,$app[6],0);
    $pdf->Ln();
    //doctor
    $pdf->Cell(18,10,'Doctor:',0);
    $pdf->Cell(40,10,$app[3],0);
    $pdf->Ln();
    //reason
    $pdf->Cell(18,10,'Reason:',0);
    $pdf->Cell(40,10,$app[2],0);

  }
  
  // visit
  $pdf->Ln(20);
  // Move to the right
  $pdf->Cell(70);
  $pdf->SetFont('Arial','B',15);
  $pdf->Cell(52,10,'Visit',0,0,'C');
  $pdf->Ln();
  
  $pdf->SetFont('Arial','B',10);

  // Blood Pressure
  $pdf->Cell(30,10,'Blood Pressure:',0);
  $pdf->Cell(60,10,'LOL',0);
  $pdf->Ln();

  // Weight
  $pdf->Cell(30,10,'Weight:',0);
  $pdf->Cell(60,10,'LOL',0);
  $pdf->Ln();
  
  // Symptoms
  $pdf->Cell(30,10,'Symptoms:',0);
  $pdf->Cell(60,10,'LOL',0);
  $pdf->Ln();

  // Diagnosis
  $pdf->Cell(30,10,'Diagnosis:',0);
  $pdf->Cell(60,10,'LOL',0);
  $pdf->Ln();

  // Medicine
  $pdf->Cell(30,10,'Medicine:',0);
  $pdf->Cell(60,10,'LOL',0);
  $pdf->Ln();

  // Dosage
  $pdf->Cell(30,10,'Blood Pressure:',0);
  $pdf->Cell(60,10,'LOL',0);
  $pdf->Ln();

  // Blood Pressure
  $pdf->Cell(30,10,'Blood Pressure:',0);
  $pdf->Cell(60,10,'LOL',0);
  $pdf->Ln();

  // Blood Pressure
  $pdf->Cell(30,10,'Blood Pressure:',0);
  $pdf->Cell(60,10,'LOL',0);
  $pdf->Ln();

  // Blood Pressure
  $pdf->Cell(30,10,'Blood Pressure:',0);
  $pdf->Cell(60,10,'LOL',0);
  $pdf->Ln();

  // Blood Pressure
  $pdf->Cell(30,10,'Blood Pressure:',0);
  $pdf->Cell(60,10,'LOL',0);
  $pdf->Ln();

  // Blood Pressure
  $pdf->Cell(30,10,'Blood Pressure:',0);
  $pdf->Cell(60,10,'LOL',0);
  $pdf->Ln();

  // Blood Pressure
  $pdf->Cell(30,10,'Blood Pressure:',0);
  $pdf->Cell(60,10,'LOL',0);
  $pdf->Ln();

  // Blood Pressure
  $pdf->Cell(30,10,'Blood Pressure:',0);
  $pdf->Cell(60,10,'LOL',0);
  $pdf->Ln();

  // Blood Pressure
  $pdf->Cell(30,10,'Blood Pressure:',0);
  $pdf->Cell(60,10,'LOL',0);
  $pdf->Ln();
  
  $pdf->Output();
  
}
?>