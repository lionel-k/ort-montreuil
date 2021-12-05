<?php

// --- Parse des fichiers et récuperation des données de la BDD
$json = 'dataset-5-json.json';
if (file_exists($json)) {
    function readJson($json)
    {
        $data = file_get_contents($json);
        $obj = json_decode($data);
        return $obj;
    }

    $json_array = readJson($json);

    foreach ($json_array as $json) :
        $tab_json[] = [
            'arrival_date' => $json->arrival_date,
            'departure_date' => $json->departure_date,
            'location' => $json->location,
            'transportation_modes' => $json->transportation_modes,
            'genre' => $json->genre,
            'hotel' => $json->hotel,
        ];
    endforeach;
}

$xmlFile = 'dataset-5-xml.xml';
if (file_exists($xmlFile)) {
    $xml_array = simplexml_load_file($xmlFile);
    foreach ($xml_array as $xml) :
        $tab_xml[] = [
            'arrival_date' => (string) $xml->arrival_date,
            'departure_date' => (string) $xml->departure_date,
            'location' => (string) $xml->location,
            'transportation_modes' => (string) $xml->transportation_modes,
            'genre' => (string) $xml->genre,
            'hotel' => (string) $xml->hotel,
        ];
    endforeach;
}

$pdo = new PDO('sqlite:dataset-5-db.db');
$statement = $pdo->query("SELECT arrival_date, departure_date, location, transportation_modes, genre, hotel FROM tourism");
$tab_bdd = $statement->fetchAll(PDO::FETCH_ASSOC);

if (!empty($tab_bdd) && !empty($tab_json) && !empty($tab_xml)) {
    $tab_merge = array_merge($tab_bdd, $tab_json, $tab_xml);
}

// Ecrire le résultat dans un fichier CSV

if ($f = @fopen('dataset-5-csv.csv', 'w')) {
    foreach ($tab_merge as $ligne) {
        fputcsv($f, $ligne, ";");
    }
    fclose($f);
} else {
    echo "Impossible d'écrire dans le fichier.";
}


$jsonHotelPriceFile = 'dataset-5-hotel-prices.json';
if (file_exists($jsonHotelPriceFile)) {
    function readJsonHotelPrice($jsonHotelPriceFile)
    {
        $data = file_get_contents($jsonHotelPriceFile);
        $obj = json_decode($data);
        return $obj;
    }

    $jsonHotelPrice_array = readJsonHotelPrice($jsonHotelPriceFile);

    foreach ($jsonHotelPrice_array as $json) :
        $tabHotelPrice_json[] = [
            'hotel' => $json->hotel,
            'price_per_night' => $json->price_per_night,
        ];
    endforeach;
}

// --- Transformer le tableau afin de pouvoir utilisé les valeurs

function changeGenre($pGenre)
{
    if ($pGenre == 'F' || $pGenre == 'Fem' || $pGenre == 'fem' || $pGenre == 'Female') {
        return 'F';
    } elseif ($pGenre == 'M' || $pGenre == 'Masc' || $pGenre == 'masc' || $pGenre == 'Male') {
        return 'M';
    }
}

function changeSeparateur($pLine)
{
    if (strpos($pLine, '|') !== false) {
        return str_replace('|', ';', $pLine);
    } elseif (strpos($pLine, '-') !== false) {
        return str_replace('-', ';', $pLine);
    } elseif (strpos($pLine, ';') !== false) {
        return str_replace(';', ';', $pLine);
    }
}

// Tranformer avec le même type de genre M/F
// Transformer les dates arrival_date et departure_date (Tout en francais 30/07/2010 par ex)
foreach ($tab_merge as $line) :
    $locationTransforme = changeSeparateur($line['location']);
    $unelocation = explode(";", $locationTransforme);
    $price = 0;
    $arrival_date = DateTime::createFromFormat('m-d-Y', $line['arrival_date']);
    $newArrival_date = $arrival_date->format('d-m-Y');
    $newDeparture_date = date('d-m-Y', strtotime($line['departure_date']));
    $countNuite = date_diff(new DateTime($newArrival_date), new DateTime($newDeparture_date));
    $countNuite = intval($countNuite->format('%a'));

    foreach ($tabHotelPrice_json as $line2) {
        if ($line2['hotel'] == $line['hotel']) {
            $price = $line2['price_per_night'];
            break;
        }
    }

    $price_total = $price * $countNuite;

    $tab_base[] = [
        'arrival_date' => $newArrival_date,
        'departure_date' =>  $newDeparture_date,
        'location' =>  $locationTransforme,
        'country' => trim($unelocation[1]),
        'city' => trim($unelocation[0]),
        'transportation_modes' => changeSeparateur($line['transportation_modes']),
        'genre' => changeGenre($line['genre']),
        'hotel' =>  $line['hotel'],
        'price_total' => $price_total,
    ];
endforeach;


// Tableau qui me permet d'avoir chaque transportation_modes //Split
foreach ($tab_base as $line) :
    $lesModesTransports = explode(";", $line["transportation_modes"]);
    foreach ($lesModesTransports as $unModeTransport) :
        $tabTransformSplitElem[] = [
            'arrival_date' => $line['arrival_date'],
            'departure_date' => $line['departure_date'],
            //'location' => $line['location'],
            'country' => $line['country'],
            'city' => $line['city'],
            'transportation_modes' => trim($unModeTransport),
            'genre' => $line['genre'],
            'hotel' => $line['hotel'],
            'price_total' => $line['price_total'],
        ];
    endforeach;
endforeach;


/**
 * Question n°1
 */
$tabCombi_CountryAndModeTransport = array();
foreach ($tabTransformSplitElem as $line) :
    $tabCombi_CountryAndModeTransport[] = [
        $line['country'], $line['transportation_modes']
    ];
endforeach;

foreach ($tabCombi_CountryAndModeTransport as $line) {
    
    $separator[implode(',', $line)][] = 1;
    /* Boucler sur les résultats et les regrouper dans un nouveau tableau basé sur la combi comme clé de tableau
        Array 1 : Qui rassemble country et transportation_modes en les séparent avec une virgule
    ['Egypt,ferry'] 
    */
    /*  Array 2 : Qui ajoute autant le nombre de fois de combinaison
        0 => int 1
        1 => int 1
        2 => int 1 */
}


$tabCount_CombiCountryAndModeTransport = [];
array_walk($separator, function ($v, $k) use (&$tabCount_CombiCountryAndModeTransport) {
    $tabCount_CombiCountryAndModeTransport[] = array_merge(explode(',', $k), [count($v)]); // explode permet de diviser
    // echo $k ===> Bangladesh,airplane;
    // print_r($v) ===> Array ( [0] => 1 [1] => 1 [2] => 1 [3] .... [55] => 1 [56] => 1 [57] => 1 );
});
/*var_dump($tabCount_CombiCountryAndModeTransport);
array (size=238)
  0 => 
    array (size=3)
      0 => string 'Bangladesh' (length=10)
      1 => string 'airplane' (length=8)
      2 => int 58*/

// Permet de créer un tableau avec chaque country leurs transportation_modes et count
foreach ($tabCount_CombiCountryAndModeTransport as $key => $value) {
    $tab_transforme[$value[0]][] = array('transportation_modes' => $value[1], 'count' => $value[2]);
}

// Récuperer la liste de tte le noms des country de facon unique
foreach ($tabCount_CombiCountryAndModeTransport as $line) {
    $tab_country[] = $line[0];
}
$tab_country = array_unique($tab_country);

// Trier les valeurs (surtout pour les 'transportation_modes') en fonction du tableau de la country 
foreach ($tab_country as $line) {
    array_multisort($tab_transforme[$line]);
}

foreach ($tab_country as $line) {
    if (!empty($tab_transforme[$line])) {
        if ($f = @fopen("question-1\\transportation-modes-" . $line . '.csv', 'w')) {
            fwrite($f, "transportation_mode;count \r\n"); //Entete
            foreach ($tab_transforme[$line] as $ligne) {
                fputcsv($f, $ligne, ";");
            }
            fclose($f);
        }
        echo "Create " . $line . '.csv' . '</br>';
    } else {
        echo "Impossible d'écrire dans le fichier.";
    }
}

/**
 * Question n°2
 */
//Permet de créer un tableau avec chaque country le total du prix en fonction du jour de nuité
foreach ($tab_base as $key => $value) {
    $tab_transforme_q2[$value['country']][] = $value['price_total'];
}

// Constituer le tableau final avec les pays et la somme total du prix depensé par country
foreach ($tab_country as $line) {
    $tab_final_q2[] = ['country' => $line, 'price_total' => (array_sum($tab_transforme_q2[$line]))];
}

// Sort array column price_total
$keys = array_column($tab_final_q2, 'price_total');
array_multisort($keys, SORT_DESC, $tab_final_q2);


if (!empty($tab_final_q2)) {
    if ($f = @fopen('question-2\\countries-revenues.csv', 'w')) {
        fwrite($f, "country;hotel_revenue \r\n"); //Entete
        foreach ($tab_final_q2 as $ligne) {
            fputcsv($f, $ligne, ";");
        }
        fclose($f);
        echo "Create countries-revenues.csv" . '</br>';
    } else {
        echo "Impossible d'écrire dans le fichier.";
    }
}
