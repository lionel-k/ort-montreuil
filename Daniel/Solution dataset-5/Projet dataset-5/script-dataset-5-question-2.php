<?php
include './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


//Read the CSV File
$reader = new Csv();
$spreadsheet = $reader->load("dataset-5-csv.csv");

$sheetData = $spreadsheet->getActiveSheet()->toArray();

function diffBetween2Date($arrive, $depart)
{
    $arrive = orderDate($arrive);
    $depart = orderDate($depart);
    $date1 = strtotime($arrive);
    $date2 = strtotime($depart);
    $dateDiff = $date2 - $date1;

    return round($dateDiff / (60 * 60 * 24));
}

function orderDate($date)
{

    $date = explode("-", $date);
    $property0 = $date[0];
    $property1 = $date[1];
    $property2 = $date[2];

    switch ($property1) {
        case 'Jan':
            $dateFinale = $property0 . '-01-' . $property2;
            break;
        case 'Feb':
            $dateFinale = $property0 . '-02-' . $property2;
            break;
        case 'Mar':
            $dateFinale = $property0 . '-03-' . $property2;
            break;
        case 'Apr':
            $dateFinale = $property0 . '-04-' . $property2;
            break;
        case 'May':
            $dateFinale = $property0 . '-05-' . $property2;
            break;
        case 'Jun':
            $dateFinale = $property0 . '-06-' . $property2;
            break;
        case 'Jul':
            $dateFinale = $property0 . '-07-' . $property2;
            break;
        case 'Aug':
            $dateFinale = $property0 . '-08-' . $property2;
            break;
        case 'Sep':
            $dateFinale = $property0 . '-09-' . $property2;
            break;
        case 'Oct':
            $dateFinale = $property0 . '-10-' . $property2;
            break;
        case 'Nov':
            $dateFinale = $property0 . '-11-' . $property2;
            break;
        case 'Dec':
            $dateFinale = $property0 . '-12-' . $property2;
            break;
        default:
            $dateFinale = $property1 . "-" . $property0 . "-" . $property2;
            break;
    }
    return $dateFinale;
}

function trimString($str)
{
    return trim($str);
}

//Parse le pays et la ville pour retourner le pays
function explodeCountrieCity($string)
{
    $result = null;

    if (stristr($string,  '|') !== false) {
        $result = explode('|', $string);
        $result = trim($result[1]);
    } elseif (stristr($string,  ';') !== false) {
        $result = explode(';', $string);
        $result = trim($result[1]);
    } elseif (stristr($string,  '-') !== false) {
        $result = explode('-', $string);
        $result = trim($result[1]);
    }

    return $result;
}

$tblAllCountrie = [];
$tblAllHotelPerContrie = [];
// Remplir un tableau de tous les pays présents dans le fichier
foreach ($sheetData as $key => $value) {
    $tblAllHotel = array();
    if ($key != 0) {
        $dateArrive = $value[0];
        $dateDepart = $value[1];
        $countrieAndCity = $value[2];
        $hotel = $value[5];

        $countrie = explodeCountrieCity($countrieAndCity);

        $nbDay = diffBetween2Date($dateArrive, $dateDepart);

        if (!array_key_exists($countrie, $tblAllCountrie)) {
            $tblAllCountrie[$countrie] = null;
        }

        $tblAllHotel = array($countrie, $hotel, $nbDay);
        $tblAllHotelPerContrie[] = $tblAllHotel;
    }
}



//Read the JSON File
$jsonFile = file_get_contents('dataset-5-json.json');
$jsonData = json_decode($jsonFile);

foreach ($jsonData as $key => $value) {
    $tblAllHotel = array();
    $dateArrive = $value->arrival_date;
    $dateDepart = $value->departure_date;
    $countrieAndCity = $value->location;
    $hotel = $value->hotel;

    $countrie = explodeCountrieCity($countrieAndCity);

    $nbDay = diffBetween2Date($dateArrive, $dateDepart);

    if (!array_key_exists($countrie, $tblAllCountrie)) {
        $tblAllCountrie[$countrie] = null;
    }

    $tblAllHotel = array($countrie, $hotel, $nbDay);
    $tblAllHotelPerContrie[] = $tblAllHotel;
}


//Read XML File

$xml = simplexml_load_file('dataset-5-xml.xml');
$dataXml = $xml->row;

foreach ($dataXml as $key => $value) {
    $tblAllHotel = array();
    $dateArrive = $value->arrival_date;
    $dateDepart = $value->departure_date;
    $countrieAndCity = $value->location;
    $hotel = $value->hotel;
    $hotel = trim($hotel);

    $countrie = explodeCountrieCity($countrieAndCity);

    $nbDay = diffBetween2Date($dateArrive, $dateDepart);

    if (!array_key_exists($countrie, $tblAllCountrie)) {
        $tblAllCountrie[$countrie] = null;
    }

    $tblAllHotel = array($countrie, $hotel, $nbDay);
    $tblAllHotelPerContrie[] = $tblAllHotel;
}


//Read the JSON File Prices of hotels
$jsonFilePrice = file_get_contents('dataset-5-hotel-prices.json');
$jsonDataPrice = json_decode($jsonFilePrice);

foreach ($jsonDataPrice as $key => $value) {

    foreach ($tblAllHotelPerContrie as $key2 => $value2) {
        if ($value->hotel == $value2[1]) {
            $tblAllHotelPerContrie[$key2][2] = $value2[2] * $value->price_per_night;
        }
    }
}

foreach ($tblAllHotelPerContrie as $key => $value) {
    foreach ($tblAllCountrie as $nomPay => $revenu) {
        if ($nomPay == $value[0]) {
            $tblAllCountrie[$nomPay] = $revenu + $value[2];
        }
    }
}

//On met le max en premiere valeur du tableau
arsort($tblAllCountrie);



//Ecrire les données : question 2 :

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
    ->setCellValue("A1", "country,hotel_revenue");

foreach ($tblAllCountrie as $key => $value) {
    $spreadSheetWriter->getActiveSheet()->getRowDimension($chiffre)->setRowHeight(20);

    $spreadSheetWriter->getActiveSheet()
        ->setCellValue("$celluleA", $key . "," . $value);

    //on passe au chiffre suivant et on l'applique à la position cellule
    $chiffre = $chiffre + 1;
    $celluleA = $lettreA . $chiffre;
}

$nomFichier = "countries-revenues";

$writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadSheetWriter);
$writer->save("question2/" . $nomFichier . ".csv");
