<?php
include './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

//Read the CSV File of teams
$reader = new Csv();
$spreadsheet = $reader->load("dataset-6/kpis/dataset-6-tickets-kpis.csv");

$tblKpis = $spreadsheet->getActiveSheet()->toArray();

//Tableau avec les kpis et leur trimestre respectif
$tblTrimestre = [];
for ($i = 1; $i <= 4; $i++) {
    $tblTrimestre[$i] = null;
}
//Tableau des résulats attendus : tickets done per quarter
$tblDonePerQuarter = [];

foreach ($tblKpis as $key => $kpi) {
    if ($key != 0) {
        $trimestre = getTrimestre($kpi[1]);
        $tblTrimestre[$trimestre][] = $kpi;
    }
}


foreach ($tblTrimestre as $key => $trimestre) {

    $nbTicketDone = count($trimestre);

    $tblDonePerQuarter["q" . $key] = $nbTicketDone;
}

//Ecrire les données :

$spreadSheetWriter = new Spreadsheet();

$sheet = $spreadSheetWriter->getActiveSheet();
$spreadSheetWriter->getDefaultStyle()
    ->getFont()
    ->setName('Arial')
    ->setSize(12);

// Coordonnées cellules
$lettreA  = 'A';
$chiffre = '2';
$celluleA = $lettreA . $chiffre;

$spreadSheetWriter->getActiveSheet()
    ->setCellValue("A1", "quarter,dev_capacity");

foreach ($tblDonePerQuarter as $key => $value) {
    $spreadSheetWriter->getActiveSheet()->getRowDimension($chiffre)->setRowHeight(20);

    $spreadSheetWriter->getActiveSheet()
        ->setCellValue("$celluleA", $key . "," . $value);

    //on passe au chiffre suivant et on l'applique à la position cellule
    $chiffre = $chiffre + 1;
    $celluleA = $lettreA . $chiffre;
}

$nomFichier = "tickets_done_per_quarter";

$writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadSheetWriter);
$writer->save("question-kpis/" . $nomFichier . ".csv");



// Retourne à quel trimestre appartient une date
function getTrimestre($date)
{
    $date = explode("-", $date);
    $property1 = $date[1];

    switch ($property1) {
        case 'Jan':
            $trimestre = "1";
            break;
        case 'Feb':
            $trimestre = "1";
            break;
        case 'Mar':
            $trimestre = "1";
            break;
        case 'Apr':
            $trimestre = "2";
            break;
        case 'May':
            $trimestre = "2";
            break;
        case 'Jun':
            $trimestre = "2";
            break;
        case 'Jul':
            $trimestre = "3";
            break;
        case 'Aug':
            $trimestre = "3";
            break;
        case 'Sep':
            $trimestre = "3";
            break;
        case 'Oct':
            $trimestre = "4";
            break;
        case 'Nov':
            $trimestre = "4";
            break;
        case 'Dec':
            $trimestre = "4";
            break;
        default:
            break;
    }
    return $trimestre;
}


// Met la date dans l'ordre
function orderDate($date)
{
    $date = explode("-", $date);
    $property0 = $date[0];
    $property1 = $date[1];
    $property2 = $date[2];

    switch ($property1) {
        case 'Jan':
            $dateFinale = $property2 . '-01-' . $property0;
            break;
        case 'Feb':
            $dateFinale = $property2 . '-02-' . $property0;
            break;
        case 'Mar':
            $dateFinale = $property2 . '-03-' . $property0;
            break;
        case 'Apr':
            $dateFinale = $property2 . '-04-' . $property0;
            break;
        case 'May':
            $dateFinale = $property2 . '-05-' . $property0;
            break;
        case 'Jun':
            $dateFinale = $property2 . '-06-' . $property0;
            break;
        case 'Jul':
            $dateFinale = $property2 . '-07-' . $property0;
            break;
        case 'Aug':
            $dateFinale = $property2 . '-08-' . $property0;
            break;
        case 'Sep':
            $dateFinale = $property2 . '-09-' . $property0;
            break;
        case 'Oct':
            $dateFinale = $property2 . '-10-' . $property0;
            break;
        case 'Nov':
            $dateFinale = $property2 . '-11-' . $property0;
            break;
        case 'Dec':
            $dateFinale = $property2 . '-12-' . $property0;
            break;
        default:
            break;
    }
    return $dateFinale;
}
