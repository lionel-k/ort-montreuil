<?php
include './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

//BDD
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "ort-conception-sid-1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed:"  . $conn->connect_error);
}

//Read the CSV File
$reader = new Csv();
$spreadsheet = $reader->load("dataset-4-csv.csv");

$sheetData = $spreadsheet->getActiveSheet()->toArray();

foreach ($sheetData as $key => $contrat) {

    if ($key != 0) {

        $contract_id    = $contrat[0];
        $hotel_name     = $contrat[1];
        $lst_ingredient = $contrat[2];

        $tbl_ingredient = explode(",", $lst_ingredient);


        $req = "INSERT IGNORE INTO `contrat`(`contract_id`, `hotel_name`) VALUES ('" . $contract_id . "', '" . $hotel_name . "')";

        if ($conn->query($req) !== TRUE) {
            //echo "Error: " . $req . "<br>" . $conn->error;
        }

        foreach ($tbl_ingredient as $key => $ingredient) {
            $req2 = "INSERT IGNORE INTO `contrat_ingredient`(`contract_id`, `ingredient_name`) VALUES ('" . $contract_id . "', '" . $ingredient . "')";

            if ($conn->query($req2) !== TRUE) {
                //echo "Error: " . $req2 . "<br>" . $conn->error;
            }
        }
    }
}


//Read the JSON File
$jsonFile = file_get_contents('dataset-4-json.json');
$jsonData = json_decode($jsonFile);

foreach ($jsonData as $key => $value) {

    $contract_id = $value->contract_id;
    $hotel_name = $value->hotel_name;
    $lst_ingredient = $value->ingredients;

    $tbl_ingredient = explode(",", $lst_ingredient);

    $req = "INSERT IGNORE INTO `contrat`(`contract_id`, `hotel_name`) VALUES ('" . $contract_id . "', '" . $hotel_name . "')";

    if ($conn->query($req) !== TRUE) {
        echo "Error: " . $req . "<br>" . $conn->error;
    }

    foreach ($tbl_ingredient as $key => $ingredient) {
        $req2 = "INSERT IGNORE INTO `contrat_ingredient`(`contract_id`, `ingredient_name`) VALUES ('" . $contract_id . "', '" . $ingredient . "')";

        if ($conn->query($req2) !== TRUE) {
            echo "Error: " . $req2 . "<br>" . $conn->error;
        }
    }
}


//Read XML File

$xml = simplexml_load_file('dataset-4-xml.xml');
$dataXml = $xml->row;

foreach ($dataXml as $key => $value) {

    $contract_id = $value->contract_id;
    $hotel_name = $value->hotel_name;
    $lst_ingredient = $value->ingredients;

    $tbl_ingredient = explode(",", $lst_ingredient);

    $req = "INSERT IGNORE INTO `contrat`(`contract_id`, `hotel_name`) VALUES ('" . $contract_id . "', '" . $hotel_name . "')";

    if ($conn->query($req) !== TRUE) {
        echo "Error: " . $req . "<br>" . $conn->error;
    }

    foreach ($tbl_ingredient as $key => $ingredient) {
        $req2 = "INSERT IGNORE INTO `contrat_ingredient`(`contract_id`, `ingredient_name`) VALUES ('" . $contract_id . "', '" . $ingredient . "')";

        if ($conn->query($req2) !== TRUE) {
            echo "Error: " . $req2 . "<br>" . $conn->error;
        }
    }
}


//Requete SQL pour sélectionner les hôtels à qui on n'a pas encore vendu d'ingrédients
$select = "SELECT distinct hotels.hotel_name FROM hotels WHERE hotels.hotel_name NOT IN( SELECT distinct hotels.hotel_name FROM contrat_ingredient INNER JOIN ingredients ON contrat_ingredient.`ingredient_name` = ingredients.`ingredient_name` INNER JOIN contrat ON contrat_ingredient.`contract_id` = contrat.`contract_id` INNER JOIN hotels ON contrat.hotel_name = hotels.hotel_name )";

if ($conn->query($select) !== false) {
    $result = $conn->query($select);

    $spreadSheetWriter = new Spreadsheet();

    $sheet = $spreadSheetWriter->getActiveSheet();
    $spreadSheetWriter->getDefaultStyle()
        ->getFont()
        ->setName('Arial')
        ->setSize(12);

    // Coordonnées cellules
    $lettre  = 'A';
    $chiffre = '1';
    $cellule = $lettre . $chiffre;


    while ($row = $result->fetch_assoc()) {

        $spreadSheetWriter->getActiveSheet()->getRowDimension($chiffre)->setRowHeight(20);
        $spreadSheetWriter->getActiveSheet()
            ->getColumnDimension("$lettre")->setWidth(20);

        $spreadSheetWriter->getActiveSheet()
            ->setCellValue("$cellule", $row["hotel_name"]);

        //on passe au chiffre suivant et on l'applique à la position cellule
        $chiffre = $chiffre + 1;
        $cellule = $lettre . $chiffre;
    }

    //on prend la date pour la mettre dans le nom du fichier
    date_default_timezone_set('Europe/Paris');
    $date = date('Y-m-d');
    $nomFichier = "Hôtels list - " . $date;

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadSheetWriter);
    $writer->save("docs/" . $nomFichier . ".csv");
} else {
    throw new Exception("Error Select", 1);
}
