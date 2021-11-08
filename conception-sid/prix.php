<?php

function read($csv){
    $file = fopen($csv, 'r');
    while (!feof($file) ) {
        $line[] = fgetcsv($file, 1024);
    }
    fclose($file);
    return $line;
}

$prices_array =["25CB99" => 31,
"87A4E4" => 38,
"CC2B77" =>	46,
"AED206" => 39,
"9C88F3" =>	25,
"ECC5CE" => 36,
"77AA3D" => 44,
"D8470A" => 21,
"647EED" => 40,
"4E3B4C" => 28 ];

// Définir le chemin d'accès au fichier CSV
$csv = 'dataset-3-modified.csv';

$array_prices_s = [];

$csv_array = read($csv);
// var_dump($csv_array);
// echo '<pre>';
// print_r($csv_array);
// echo '</pre>';
// array_prices_s

// foreach($csv_array as  $key => $csv)
// {
//     var_dump($csv[$i]);
// }
for($i = 7; $i<16; $i++)
{
    foreach($csv_array as  $key => $csv)
    {
        var_dump($csv[$i]);
    }
}

