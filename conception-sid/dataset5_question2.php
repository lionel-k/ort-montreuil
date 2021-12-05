<!-- Donnez les données en commençant par le pays avec le plus de revenus. par exemple, pays-revenus.csv -->
<?php

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

$json = 'dataset-5-json.json';
$json_array = readJson($json);

foreach($json_array as $json):
    $lstJson[] = 
        [
            'arrival_date' => $json->arrival_date,
            'departure_date' => $json->departure_date,
            'location' => $json->location,
            'transportation_modes' => $json->transportation_modes,
            'genre' => $json->genre,
            'hotel' => $json->hotel,
        ];
endforeach;

$json = 'dataset-5-hotel-prices.json';
$json_array = readJson($json);

foreach($json_array as $json):
    $lstPrices[] = 
        [
            'hotel' => $json->hotel,
            'price_per_night' => $json->price_per_night
        ];
endforeach;




$csv = 'data5_db.csv';
$csv_array = readCsv($csv);
$lstDb = array();
foreach($csv_array as $csv):
    $lstDb[] = 
        [
        'arrival_date' => $csv[0],
        'departure_date' => $csv[1],
        'location' => $csv[2],
        'transportation_modes' => $csv[3],
        'genre' => $csv[4],
        'hotel' => $csv[5],
        ];
endforeach;

$xml = 'dataset-5-xml.xml';
$xml_array = simplexml_load_file($xml);

foreach($xml_array as $xml):
    $lstXml[] = 
    [
        'arrival_date' => $xml->arrival_date,
        'departure_date' => $xml->departure_date,
        'location' => $xml->location,
        'transportation_modes' => $xml->transportation_modes,
        'genre' => $xml->genre,
        'hotel' => $xml->hotel,
    ];
endforeach;

// $lst_tab = $lstDb + $lstJson + $lstXml;
$lst_tab = array_merge($lstDb, $lstJson, $lstXml);


$separateur = array('-', '|');
$genre_femme = array('F','Fem', 'fem', 'Female');
$genre_male = array('M', 'Masc', 'masc', 'Male');

// mettre toutes les donnees dans le bon format
foreach($lst_tab as $lst):

    $location = str_replace($separateur, ';', $lst['location']);
    $location = str_replace(" ", "", $location);
    $arrival_date = DateTime::createFromFormat('m-d-Y', $lst['arrival_date']);
    if($arrival_date):
        $newArrival_date = $arrival_date->format('d-m-Y');
        $newDeparture_date = date('d-m-Y', strtotime($lst['departure_date']));
        $countNuite = date_diff(new DateTime($newArrival_date), new DateTime($newDeparture_date));
        $countNuite = intval($countNuite->format('%a'));

        foreach ($lstPrices as $lst2) {
            if ($lst2['hotel'] == $lst['hotel']) {
                $price = $lst2['price_per_night'];
                break;
            }
        }

        $price_total = $price * $countNuite;

        $location = explode(";", $location);
        if(isset($location[1])):
            $pays = $location[1];
                $lst_pays_hotel[] = 
                [
                    "pays" => $pays,
                    "hotel" => $lst['hotel'],
                    "nbNuits" => $countNuite,
                    "prix" => $price_total
                ];
        endif;

    endif;
endforeach;

array_multisort($lst_pays_hotel);

$old_pays = "";
$old_hotel="";
// $i=0;
$k=0;
$tab_count=array();

$price_hotels=0;

foreach($lst_pays_hotel as $pays_hotel):
    $k++;
    if($pays_hotel['pays'] == $old_pays){            
            $tab_count=array();
            $tab_count[]=[
                "location" => $pays_hotel['pays'],
                "hotel_revenus" => $price_hotels + $pays_hotel['prix']
            ];         
            if($k == count($lst_pays_hotel))
            {
                $fp = fopen("dataset5_hotels.csv", "a");  
                foreach($tab_count as $data){
                       fputcsv($fp, $data);
                   }
               fclose($fp);  
            }  
    }else{
        $fp = fopen("dataset5_hotels.csv", "a");  
            foreach($tab_count as $data){
                fputcsv($fp, $data);
            }
        fclose($fp);  
        $price_hotels = 0;
        $tab_count=array();
        $tab_count[]=[
            "location" => $pays_hotel['pays'],
            "hotel_revenus" => $pays_hotel['prix']
        ];
    }
    $price_hotels = $price_hotels + $pays_hotel['prix'] ;
    $old_pays = $pays_hotel['pays'] ;
endforeach;
// on recuperer les valuers dans le fichiers csv, on trie et on les remet
$csv = 'dataset5_hotels.csv';
$csv_array = readCsv($csv);
$lst_hotels = array();
foreach($csv_array as $csv):
    $lst_hotels[] = 
        [
            'pays' => $csv[0],
            'hotel_revenue' => $csv[1]
        ];
endforeach;

$columns = array_column($lst_hotels, 'hotel_revenue');
array_multisort($columns, SORT_DESC, $lst_hotels);
$fp = fopen("dataset5_hotels.csv", "w");  
fwrite($fp, "country,hotel_revenue \r\n");
    foreach($lst_hotels as $data){
        fputcsv($fp, $data);
    }
fclose($fp); 
