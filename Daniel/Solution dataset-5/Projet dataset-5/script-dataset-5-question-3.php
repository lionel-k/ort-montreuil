<?php
include './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;

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

function inHoliday($date)
{
    //date dd-mm-yyyy
    $date = explode("-", $date);
    $month = $date[1];

    if ($month == "07" || $month == "08") {
        return true;
    } else {
        return false;
    }
}

$tblAllHotelRevenu = [];


//Read the DB File
$pdo = new PDO('sqlite:dataset-5-db.db');
$statement = $pdo->query("SELECT arrival_date, departure_date, location, transportation_modes, genre, hotel FROM tourism");
$dbData = $statement->fetchAll(PDO::FETCH_ASSOC);


// Remplir un tableau de tous les pays présents dans le fichier
foreach ($dbData as $key => $value) {

    if ($key != 0) {
        $hotel = $value['hotel'];
        $dateArrive = $value["arrival_date"];
        $dateDepart = $value["departure_date"];

        $nbDay = diffBetween2Date($dateArrive, $dateDepart);

        $dateArrive = orderDate($dateArrive);
        $dateDepart = orderDate($dateDepart);

        if (inHoliday($dateArrive) || inHoliday($dateDepart)) {
            if (!array_key_exists($hotel, $tblAllHotelRevenu)) {
                $tblAllHotelRevenu[$hotel] = $nbDay;
            } else {
                $tblAllHotelRevenu[$hotel] = $tblAllHotelRevenu[$hotel] + $nbDay;
            }
        }
    }
}



//Read the JSON File
$jsonFile = file_get_contents('dataset-5-json.json');
$jsonData = json_decode($jsonFile);

foreach ($jsonData as $key => $value) {
    $hotel = $value->hotel;
    $dateArrive = $value->arrival_date;
    $dateDepart = $value->departure_date;

    $nbDay = diffBetween2Date($dateArrive, $dateDepart);

    $dateArrive = orderDate($dateArrive);
    $dateDepart = orderDate($dateDepart);

    if (inHoliday($dateArrive) || inHoliday($dateDepart)) {
        if (!array_key_exists($hotel, $tblAllHotelRevenu)) {
            $tblAllHotelRevenu[$hotel] = $nbDay;
        } else {
            $tblAllHotelRevenu[$hotel] = $tblAllHotelRevenu[$hotel] + $nbDay;
        }
    }
}

//Read XML File

$xml = simplexml_load_file('dataset-5-xml.xml');
$dataXml = $xml->row;

foreach ($dataXml as $key => $value) {
    $hotel = trim($value->hotel);
    $dateArrive = $value->arrival_date;
    $dateDepart = $value->departure_date;

    $nbDay = diffBetween2Date($dateArrive, $dateDepart);

    $dateArrive = orderDate($dateArrive);
    $dateDepart = orderDate($dateDepart);

    if (inHoliday($dateArrive) || inHoliday($dateDepart)) {
        if (!array_key_exists($hotel, $tblAllHotelRevenu)) {
            $tblAllHotelRevenu[$hotel] = $nbDay;
        } else {
            $tblAllHotelRevenu[$hotel] = $tblAllHotelRevenu[$hotel] + $nbDay;
        }
    }
}


//Read the JSON File Prices of hotels
$jsonFilePrice = file_get_contents('dataset-5-hotel-prices.json');
$jsonDataPrice = json_decode($jsonFilePrice);

foreach ($jsonDataPrice as $key => $value) {

    foreach ($tblAllHotelRevenu as $key2 => $value2) {
        if ($value->hotel == $key2) {
            $tblAllHotelRevenu[$key2] = $value2 * $value->price_per_night;
        }
    }
}
//On met le max en premiere valeur du tableau
arsort($tblAllHotelRevenu);



//Ecrire les données : question 3 :

$ctr = 0;

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
    ->setCellValue("A1", "hotel,revenue");

foreach ($tblAllHotelRevenu as $key => $value) {

    if ($ctr <= 9) {
        $spreadSheetWriter->getActiveSheet()->getRowDimension($chiffre)->setRowHeight(20);

        $spreadSheetWriter->getActiveSheet()
            ->setCellValue("$celluleA", $key . "," . $value);
    }

    //on passe au chiffre suivant et on l'applique à la position cellule
    $chiffre = $chiffre + 1;
    $celluleA = $lettreA . $chiffre;

    $ctr = $ctr + 1;
}

$nomFichier = "hotels-revenues-";

$writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadSheetWriter);
$writer->save("question3/" . $nomFichier . ".csv");
