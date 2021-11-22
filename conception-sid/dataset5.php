<!-- Quels sont les modes de transport utilisés par pays ? -->
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