<?php
include './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


//Read the CSV File
$reader = new Csv();
$spreadsheet = $reader->load("dataset-5-csv.csv");

$sheetData = $spreadsheet->getActiveSheet()->toArray();

$tblAllCountrie = [];

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

function explodeTransportMode($string)
{
    $result = null;

    if (stristr($string,  '|') !== false) {
        $result = explode('|', $string);
    } elseif (stristr($string,  ';') !== false) {
        $result = explode(';', $string);
    } elseif (stristr($string,  '-') !== false) {
        $result = explode('-', $string);
    }

    return $result;
}

// Remplir un tableau de tous les pays présents dans le fichier
foreach ($sheetData as $key => $value) {

    if ($key != 0) {
        $countrieAndCity = $value[2];

        $countrie = explodeCountrieCity($countrieAndCity);

        if (!array_key_exists($countrie, $tblAllCountrie)) {
            $tblAllCountrie[$countrie] = null;
        }
    }
}

//On reparcours le tableau de tous les pays
foreach ($tblAllCountrie as $key => $value) {
    $tblTransportMode = [];

    foreach ($sheetData as $key2 => $value2) {

        $transportMode = $value2[3];
        $countrieAndCity = $value2[2];
        $countrie = explodeCountrieCity($countrieAndCity);
        $transportMode = explodeTransportMode($transportMode);

        //Si le pays en cours est == au pays du tableau de tous les pays on ajoute le mode de transport
        if ($countrie == $key) {

            foreach ($transportMode as $key3 => $value3) {
                $value3 = trim($value3);
                if (!array_key_exists($value3, $tblTransportMode)) {
                    $tblTransportMode[$value3] = 1;
                } else {
                    $tblTransportMode[$value3] = $tblTransportMode[$value3] + 1;
                }
            }

            $tblAllCountrie[$countrie] = $tblTransportMode;
        }
    }
}


//Read the JSON File
$jsonFile = file_get_contents('dataset-5-json.json');
$jsonData = json_decode($jsonFile);

foreach ($jsonData as $key => $value) {
    $countrieAndCity = $value->location;

    $countrie = explodeCountrieCity($countrieAndCity);

    if (!array_key_exists($countrie, $tblAllCountrie)) {
        $tblAllCountrie[$countrie] = null;
    }
}

//On reparcours le tableau de tous les pays
foreach ($tblAllCountrie as $key => $value) {
    $tblTransportMode = [];

    foreach ($jsonData as $key2 => $value2) {

        $transportMode = $value2->transportation_modes;
        $countrieAndCity = $value2->location;
        $countrie = explodeCountrieCity($countrieAndCity);
        $transportMode = explodeTransportMode($transportMode);

        //Si le pays en cours est == au pays du tableau de tous les pays on ajoute le mode de transport
        if ($countrie == $key) {

            foreach ($transportMode as $key3 => $value3) {
                $value3 = trim($value3);
                if (!array_key_exists($value3, $tblAllCountrie[$countrie])) {
                    $tblAllCountrie[$countrie][$value3] = 1;
                } else {
                    $tblAllCountrie[$countrie][$value3] = $tblAllCountrie[$countrie][$value3] + 1;
                }
            }
        }
    }
}


//Read XML File

$xml = simplexml_load_file('dataset-5-xml.xml');
$dataXml = $xml->row;

foreach ($dataXml as $key => $value) {
    $countrieAndCity = $value->location;

    $countrie = explodeCountrieCity($countrieAndCity);

    if (!array_key_exists($countrie, $tblAllCountrie)) {
        $tblAllCountrie[$countrie] = null;
    }
}

//On reparcours le tableau de tous les pays
foreach ($tblAllCountrie as $key => $value) {
    $tblTransportMode = [];

    foreach ($dataXml as $key2 => $value2) {

        $transportMode = $value2->transportation_modes;
        $countrieAndCity = $value2->location;
        $countrie = explodeCountrieCity($countrieAndCity);
        $transportMode = explodeTransportMode($transportMode);

        //Si le pays en cours est == au pays du tableau de tous les pays on ajoute le mode de transport
        if ($countrie == $key) {

            foreach ($transportMode as $key3 => $value3) {
                $value3 = trim($value3);
                if (!array_key_exists($value3, $tblAllCountrie[$countrie])) {
                    $tblAllCountrie[$countrie][$value3] = 1;
                } else {
                    $tblAllCountrie[$countrie][$value3] = $tblAllCountrie[$countrie][$value3] + 1;
                }
            }
        }
    }
}


//Ecrire les données : question 1 :

foreach ($tblAllCountrie as $key => $value) {

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
        ->setCellValue("A1", "transportation_mode,count");

    foreach ($value as $key2 => $value2) {
        $spreadSheetWriter->getActiveSheet()->getRowDimension($chiffre)->setRowHeight(20);

        $spreadSheetWriter->getActiveSheet()
            ->setCellValue("$celluleA", $key2 . "," . $value2);

        //on passe au chiffre suivant et on l'applique à la position cellule
        $chiffre = $chiffre + 1;
        $celluleA = $lettreA . $chiffre;
    }

    $nomFichier = "transports-pays-" . strtolower($key);

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadSheetWriter);
    $writer->save("SOLUTIONA/" . $nomFichier . ".csv");
}
