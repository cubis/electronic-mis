<?php
require_once('auth.php');
require_once('bootstrap.php');

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
    $this->Cell(56,10,'Appointment Receipt',0,0,'C');
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

  function Table($header, $data) {
    // Column widths
    $w = array(40, 35, 40, 45);
    // Header
    for($i=0;$i<count($header);$i++)
      $this->Cell($w[$i],7,$header[$i],1,0,'C');
    $this->Ln();
    // Data
    foreach($data as $row)
      {
        $this->Cell($w[0],6,$row[0],'LR');
        $this->Cell($w[1],6,$row[1],'LR');
        $this->Cell($w[2],6,number_format($row[2]),'LR',0,'R');
        $this->Cell($w[3],6,number_format($row[3]),'LR',0,'R');
        $this->Ln();
      }
    // Closing line
    $this->Cell(array_sum($w),0,'','T');
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
  $pdf->Output();
  
}
?>