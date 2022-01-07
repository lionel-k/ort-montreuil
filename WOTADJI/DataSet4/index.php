<?php


/**********************************************************
 * ********************************************************
CHARGEONS LES DONNEES XML, JSON, XML (FICHIER CONTENANT LES DIFFERENT INCREDIENT AVEC LICENCE DE VENTE) DANS LA BASE DE DONNEES 

 --> IL FAUDRA AVOIR UNE TABLE REGROUPANT L'ENSEMBLE DES INGREDIENTS DE TOUS LES FICHIER ( ID_CONTRAT, NOM_INREDIENT) => contrat_ingredient
 --> SAUVEGARDE DANS UNE AUTRE TABLE LES NOMS DES HOTELS PAR ID  ( ID_CONTRAT, NOM_HOTELS) =>  contrat
************************************************************
* **********************************************************
*/




// *** FICHIER CSV ***


$file0 =file('dataset-4-csv.csv');
$spreadsheet = $reader->load("dataset-4-csv.csv");
        
$sheetData = $spreadsheet->getActiveSheet()->toArray();
foreach ($sheetData as $key => $contrat) {

    if ($key != 0) {

        $contract_id    = $contrat[0];
        $hotel_name     = $contrat[1];
        $lst_ingredient = $contrat[2];

        $tbl_ingredient = explode(",", $lst_ingredient);


        $req = "INSERT IGNORE INTO `contrat`(`contract_id`, `hotel_name`) VALUES ('" . $contract_id . "', '" . $hotel_name . "')";

       foreach ($tbl_ingredient as $key => $ingredient) {
            $req2 = "INSERT IGNORE INTO `contrat_ingredient`(`contract_id`, `ingredient_name`) VALUES ('" . $contract_id . "', '" . $ingredient . "')";

            if ($conn->query($req2) !== TRUE) {
                //echo "Error: " . $req2 . "<br>" . $conn->error;
            }
        }
    }
}




// *** FICHIER JSON ***

$fileJson = file_get_contents('dataset-4-json.json');
$datas = json_decode($fileJson);

foreach ($datas as $key => $value) {

    $contract_id = $value->contract_id;
    $hotel_name = $value->hotel_name;
    $liste_ingredient = $value->ingredients;

    $table_ingredient = explode(",", $liste_ingredient);

    //echo "<pre>";
    //print_r($table_ingredient);
    //echo "</pre>";
    die();

    $req = "INSERT IGNORE INTO `contrat`(`contract_id`, `hotel_name`) VALUES ('" . $contract_id . "', '" . $hotel_name . "')";

    if ($conn->query($req) !== TRUE) {
        echo "Error: " . $req . "<br>" . $conn->error;
    }

    foreach ($table_ingredient as $key => $ingredient) {
        $req2 = "INSERT IGNORE INTO `contrat_ingredient`(`contract_id`, `ingredient_name`) VALUES ('" . $contract_id . "', '" . $ingredient . "')";

        if ($conn->query($req2) !== TRUE) {
            echo "Error: " . $req2 . "<br>" . $conn->error;
        }
    }
}



// *** FICHIER XML ***

$xml = simplexml_load_file('dataset-4-xml.xml');
$dataXml = $xml->row;

var_dump($dataXml); die();

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









 // Database configuration 
$dbHost     = "localhost"; 
$dbUsername = "root"; 
$dbPassword = "root"; 
$dbName     = "ort-conception-sid-1"; 
 
// Create database connection 
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName); 
 
// Check connection 
if ($db->connect_error) { 
    die("Connection failed: " . $db->connect_error); 
}



// Load the database configuration file  
// Fetch records from database 
$query = $db->query("SELECT distinct hotels.hotel_name FROM hotels WHERE hotels.hotel_name NOT IN( SELECT distinct hotels.hotel_name FROM contrat_ingredient INNER JOIN ingredients ON contrat_ingredient.`ingredient_name` = ingredients.`ingredient_name` INNER JOIN contrat ON contrat_ingredient.`contract_id` = contrat.`contract_id` INNER JOIN hotels ON contrat.hotel_name = hotels.hotel_name )"); 
 
if($query->num_rows > 0){ 
    $delimiter = ","; 
    $filename = "Hotel-client" . date('Y-m-d') . ".csv"; 
     
    // Create a file pointer 
    $f = fopen('php://memory', 'w'); 
     
    // Set column headers 
    $fields = array('hotel_name'); 
    fputcsv($f, $fields, $delimiter); 
     
    // Output each row of the data, format line as csv and write to file pointer 
    while($row = $query->fetch_assoc()){ 
        $status = ($row['status'] == 1)?'Active':'Inactive'; 
        $lineData = array($row['hotel_name'], $status); 
        fputcsv($f, $lineData, $delimiter); 
    } 
     
    // Move back to beginning of file 
    fseek($f, 0); 
     
    // Set headers to download file rather than displayed 
    header('Content-Type: text/csv'); 
    header('Content-Disposition: attachment; filename="' . $filename . '";'); 
     
    //output all remaining data on a file pointer 
    fpassthru($f); 
} 
exit; 
 
?>






