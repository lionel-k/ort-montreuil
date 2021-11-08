<?php

// on fais le tableau avec toutes les articles
$sku_array = [ 7 =>'25CB99', 
            8=> '87A4E4', 
            9 => 'CC2B77',
            10 => 'AED206', 
            11 => '9C88F3', 
            12 => 'ECC5CE',
            13 => '77AA3D',
            14 => 'D8470A',
            15 => '647EED',
            16 => '4E3B4C'];

$sku_units_array = [];
// on recupere les donnees, on fais pour  lignes 

// On boucle sur  article si il est present sinn 0
function read($csv){
    $file = fopen($csv, 'r');
    while (!feof($file) ) {
        $line[] = fgetcsv($file, 1024);
    }
    fclose($file);
    return $line;
}

// Définir le chemin d'accès au fichier CSV
$csv = 'dataset-3-modified.csv';

$csv_array = read($csv);
// var_dump($csv_array);
// echo '<pre>';
// print_r($csv_array);
// echo '</pre>';

$somme_array = [];

    foreach($sku_array  as $key => $sky)
    {
        // var_dump($key);
        foreach($csv_array as $csv)
        {
            // var_dump($csv[$key]);
            $somme_array[$key] = [$csv[$key]];
        }
    }

var_dump($somme_array);

?>