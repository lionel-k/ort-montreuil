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

// asort($new_tab_test);
// $ln_new = "";
// $ln_hotel = "";
// $final_tab_test = array();

// foreach($new_tab_test as $ln):
   
//     if($ln_new <> $ln['ingredients'])
//     {
//         $ln_hotel = "";
//         $final_tab_test[$ln['ingredients']] = [
//             // 'ingredients' => $ln['ingredients'],
//             'hotels' => $ln["hotels"],
//             ];
//     }else{
//         // on met pour la meme ligne on ajoute l'hotel
//         $final_tab_test[ $ln['ingredients']] = [
//             // 'ingredients' => $ln['ingredients'],
//             'hotels' => $ln_hotel."-".$ln["hotels"],
//         ];
//     }
//     $ln_new = $ln['ingredients'];
//     $ln_hotel = $ln_hotel."-".$ln['hotels'];
// endforeach;

// dans la bdd, on doit faire la requete pour avoir la liste des ingredients
// on recupere la liste des hotels par ingredients issus de leads 
$tab_leads = array();

$csv_leads = 'sql_result_2.csv';
$csv_leads_array = readCsv($csv_leads);
foreach($csv_leads_array as $csv_leads):
    $tab_leads[] = 
        [
            'ingredients' => $csv_leads[1],
            'hotels' => $csv_leads[0],
        ];
endforeach;

// $fp = fopen('leads_ingredients.csv', 'w');
// foreach ($tab_leads as $ln) {
//     fputcsv($fp, $ln, "-");
// }
// fclose($fp);

// on a deux listes 
// . liste de toutes les hotels par ingredients vendus
// . liste de tous les hotels par ingredients 


// donc ce qu'il faudrait : 
    // Garder tous les ingredients du tableaux : tab_final
    // Garder tous les hotels du tableaux leads  

// $result= array_map($new_tab_test,$tab_leads);

// var_dump($new_tab_test['ingredients']);

function searchForId($id, $array) {
    foreach ($array as $key => $val) {
        if ($val['ingredients'] === $id) {
            return $key;
        }
    }
    return null;
 }

 var_dump(count($new_tab_test));
 foreach($tab_leads as $leads):


    // VERIFIER que l'ingredients fait partie des autres ingredeints 
    $id = searchForId($leads['ingredients'], $new_tab_test);
    if(!empty($id)):
        $new_tab_test[] = [
            'ingredients' => $leads['ingredients'],
            'hotels' => $leads['hotels'],
        ];
    endif;

endforeach;
// $result = $new_tab_test + $tab_leads;
var_dump(count($new_tab_test));

asort($new_tab_test);
$final_tab_test = array();
$ln_new = "";
$ln_hotel = "";

foreach($new_tab_test as $ln):
   
    if($ln_new <> $ln['ingredients'])
    {
        $ln_hotel = "";
        $final_tab_test[] = [
            'ingredients' => $ln['ingredients'],
            'hotels' => $ln["hotels"],
            ];
    }
    // else{
    //     // on met pour la meme ligne on ajoute l'hotel
    //     $final_tab_test[] = [
    //         'ingredients' => $ln['ingredients'],
    //         'hotels' => $ln_hotel."-".$ln["hotels"],
    //     ];
    // }
    $ln_new = $ln['ingredients'];
    $ln_hotel = $ln_hotel."-".$ln['hotels'];
endforeach;



// $uniqueArray = array_unique($new_tab_test, SORT_REGULAR);



function search_revisions($dataArray, $search_value, $key_to_search, $other_matching_value = null, $other_matching_key = null) {
    $keys = array();
    foreach ($dataArray as $key => $cur_value) {
        if ($cur_value[$key_to_search] == $search_value) {
            if (isset($other_matching_key) && isset($other_matching_value)) {
                if ($cur_value[$other_matching_key] == $other_matching_value) {
                    $keys[] = $key;
                }
            } else {
                $keys[] = $key;
            }
        }
    }
    return $keys;
}

var_dump(count($final_tab_test));


$fp = fopen('result_2.csv', 'w');

foreach ($uniqueArray as $ln) {
    fputcsv($fp, $ln, ";");
}
fclose($fp);


// SELECT  hotel_name, ingredient_name FROM dishes as d, hotels as h, ingredients i

// where d.hotel_code =  h.hotel_code

// and d.dish_code = i.dish_code

// order by ingredient_name
 ?>