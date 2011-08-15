<?php
require_once('auth.php');
require_once('bootstrap.php');

restrictAccess('1000');

$userName = $_SESSION['SESS_USERNAME'];
$aid = $_GET['aid'];

global $currentPath;
$request = $currentPath . "apptViewREST.php?";
$request .= "u=" . urlencode($userName);
$request .= "&key=" . urlencode($_SESSION['SESS_AUTH_KEY']);
$request .= "&aid=" . urlencode($aid);

//echo $request;

//format and send request
$ch = curl_init($request);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);    
curl_setopt($ch, CURLOPT_TIMEOUT, 8);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$RESToutput = curl_exec($ch); //send URL Request to RESTServer... returns string
curl_close($ch); //string from server has been returned <XML> closethe channel

//die($RESToutput);

if( $RESToutput == ''){
  die("CONNECTION ERROR");
}

$status = $wsResponse[$wsIndices['STATUS'][0]]['value'];

//parse return string
$parser = xml_parser_create();	
xml_parse_into_struct($parser, $RESToutput, $wsResponse, $wsIndices);
xml_parser_free($parser);

$errNum = $wsResponse[$wsIndices['ERRNUM'][0]]['value'];
if ($errNum != 0) {
  $ct = 0;
  while($ct < $errNum){
    $err_msg_arr[] = $wsResponse[$wsIndices['ERROR'][$ct]]['value'];
    $ct++;
  }
  $_SESSION['ERRMSG_ARR'] = $err_msg_arr;
}

//echo "1<br/>" . $appID = $wsResponse[$wsIndices['APPID'][0]]['value'];
$date = $wsResponse[$wsIndices['DATE'][0]]['value'];
$time = $wsResponse[$wsIndices['TIME'][0]]['value'];
$status = $wsResponse[$wsIndices['STATUS'][0]]['value'];
$doctor = $wsResponse[$wsIndices['DocName'][0]]['value'];
$bp = $wsResponse[$wsIndices['BP'][0]]['value'];
$weight = $wsResponse[$wsIndices['WEIGHT'][0]]['value'];
$reason = $wsResponse[$wsIndices['REASON'][0]]['value'];
$diagnosis = $wsResponse[$wsIndices['DIAGNOSIS'][0]]['value'];
$symptoms = $wsResponse[$wsIndices['SYMPTOMS'][0]]['value'];
// echo "<br/>" . $medicine = $wsResponse[$wsIndices['MEDICINE'][0]]['value'];
// echo "<br/>" . $dosage = $wsResponse[$wsIndices['DOSAGE'][0]]['value'];
// echo "<br/>" . $startDate = $wsResponse[$wsIndices['STARTDATE'][0]]['value'];
// echo "<br/>" . $endDate = $wsResponse[$wsIndices['ENDDATE'][0]]['value'];
$bill = $wsResponse[$wsIndices['BILL'][0]]['value'];
$paymentplan = $wsResponse[$wsIndices['PAYMENTPLAN'][0]]['value'];
$months = $wsResponse[$wsIndices['NUMMONTHS'][0]]['value'];
//$fileLocation = $wsResponse[$wsIndices['FILE'][0]]['value'];

// $RESToutput = null;

// $request = $currentPath . "apptViewREST.php?";
// $request .= "u=" . urlencode($userName);
// $request .= "&key=" . urlencode($_SESSION['SESS_AUTH_KEY']);
// $request .= "&aid=" . urlencode($aid);

// //format and send request
// $ch = curl_init($request);
// curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);    
// curl_setopt($ch, CURLOPT_TIMEOUT, 8);
// curl_setopt($ch, CURLOPT_HEADER, false);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// $RESToutput = curl_exec($ch); //send URL Request to RESTServer... returns string
// curl_close($ch); //string from server has been returned <XML> closethe channel

// //die($RESToutput);

// if( $RESToutput == ''){
//   die("CONNECTION ERROR");
// }


// //parse return string
// $parser = xml_parser_create();	
// xml_parse_into_struct($parser, $RESToutput, $wsResponse, $wsIndices);
// xml_parser_free($parser);

// $errNum = $wsResponse[$wsIndices['ERRNUM'][0]]['value'];
// if ($errNum != 0) {
//   $ct = 0;
//   while($ct < $errNum){
//     $err_msg_arr[] = $wsResponse[$wsIndices['ERROR'][$ct]]['value'];
//     $ct++;
//   }
//   $_SESSION['ERRMSG_ARR'] = $err_msg_arr;
// }

// $apptStatus = $wsResponse[$wsIndices['STATUS'][0]]['value'];



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

//date
$pdf->Cell(18,10,'Date:',0);
$pdf->Cell(40,10,$date,0);
$pdf->Ln();
//time
$pdf->Cell(18,10,'Time:',0);
//$date = DateTime::createFromFormat('H:i:s', $time);
$pdf->Cell(40,10,$date,0);
$pdf->Ln();
//status
$pdf->Cell(18,10,'Status:',0);
$pdf->Cell(40,10,$status,0);
$pdf->Ln();
//doctor
$pdf->Cell(18,10,'Doctor:',0);
$pdf->Cell(40,10,$doctor,0);
$pdf->Ln();
//reason
$pdf->Cell(18,10,'Reason:',0);
$pdf->Cell(40,10,$reason,0);

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
$pdf->Cell(60,10,$bp,0);
$pdf->Ln();

// Weight
$pdf->Cell(30,10,'Weight:',0);
$pdf->Cell(60,10,$weight,0);
$pdf->Ln();
  
// Symptoms
$pdf->Cell(30,10,'Symptoms:',0);
$pdf->Cell(60,10,$symptoms,0);
$pdf->Ln();

// Diagnosis
$pdf->Cell(30,10,'Diagnosis:',0);
$pdf->Cell(60,10,$diagnosis,0);
$pdf->Ln();

// Medicine
$pdf->Cell(30,10,'Medicine:',0);
$pdf->Cell(60,10,"LOL",0);
$pdf->Ln();

// Dosage
$pdf->Cell(30,10,'Dosage:',0);
$pdf->Cell(60,10,'LOL',0);
$pdf->Ln();

// Start Date
$pdf->Cell(30,10,'Start Date:',0);
$pdf->Cell(60,10,'LOL',0);
$pdf->Ln();

// End Date
$pdf->Cell(30,10,'End Date:',0);
$pdf->Cell(60,10,'LOL',0);
$pdf->Ln();

// Total Bill
$pdf->Cell(30,10,'Total Bill:',0);
$pdf->Cell(60,10,$bill,0);
$pdf->Ln();

// Payment Plan
$pdf->Cell(30,10,'Payment Plan:',0);
$pdf->Cell(60,10,$paymentplan,0);
$pdf->Ln();

// Months
$pdf->Cell(30,10,'Months:',0);
$pdf->Cell(60,10,$months,0);
$pdf->Ln();

// Referal
$pdf->Cell(30,10,'Referal:',0);
$pdf->Cell(60,10,'LOL',0);
$pdf->Ln();

// File Name
$pdf->Cell(30,10,'File Name:',0);
$pdf->Cell(60,10,'LOL',0);
$pdf->Ln();

$pdf->Output();
?>