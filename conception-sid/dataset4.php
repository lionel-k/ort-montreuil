<?php
// recuperer la liste des ingredients certifiés 



function readCsv($csv){
    $file = fopen($csv, 'r');
    while (!feof($file) ) {
        $line[] = fgetcsv($file, 1024);
    }
    fclose($file);
    return $line;
}

function readJson($json)
{
    $data = file_get_contents($json); 
    $obj = json_decode($data); 
    return $obj;
}
$csv = 'dataset-4-csv.csv';
$csv_array = readCsv($csv);
$tab_test = array();
foreach($csv_array as $csv):
    $tab_test[] = 
        [
            'ingredients' => $csv[2],
            'hotels' => $csv[1],
        ];
endforeach;


// foreach($tab_test as $recipe) {
//     // echo $recipe['ingredients'] . ' contribué(e) par : ' . $recipe['hotels'] . PHP_EOL; 
// }

$json = 'dataset-4-json.json';
$json_array = readJson($json);

foreach($json_array as $json):
    $tab_test[] = 
        [
            'ingredients' => $json->ingredients,
            'hotels' => $json->hotel_name,
        ];
endforeach;


$xml = 'dataset-4-xml.xml';
$xml_array = simplexml_load_file($xml);

foreach($xml_array as $xml):
    $tab_test[] = 
    [
        'ingredients' => $xml->ingredients,
        'hotels' => $xml->hotel_name,
    ];
endforeach;

$new_tab_test = array();


// split les ingredients 
foreach($tab_test as $ln):
    $lstIngredients  = explode(",", $ln['ingredients']);
    foreach($lstIngredients as $ingredient):
        $new_tab_test[] = [
            'ingredients' => $ingredient,
            'hotels' => $ln['hotels'],
        ];
    endforeach;
endforeach;

$final_tab_test = array();
var_dump($new_tab_test);
// var_dump($new_tab_test);
// enlever les doublons
foreach($new_tab_test as $ln1):
    // foreach($new_tab_test as $ln2):
    //     // var_dump($ln1['ingredients']);
    // endforeach;
//    echo $ln1['ingredients'];


    // $tab_ingredients = ($ln1['ingredients']);
    // var_dump($tab_ingredients);
    // foreach($tab_ingredients as $ingredient):
    //     var_dump($ingredient);
    // endforeach;
    // foreach($new_tab_test as $ln2):
    //     // echo $ln1['ingredients'];
    //     // if($ln1['ingredients'] == $ln2['ingredients']):
    //     //     $final_tab_test[] = [
    //     //         'ingredients' => $ln1['ingredients'],
    //     //         'hotels' => $ln1['hotels']." ".$ln2["hotels"],
    //     //     ];
    //     // endif;
    // endforeach;
endforeach;
var_dump($final_tab_test);
?>