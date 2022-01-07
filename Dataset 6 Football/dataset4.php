<?php


$csv = 'teams.csv';
if (file_exists($csv)) {
    
    function readCsv($csv){
        $file = fopen($csv, 'r');
        while (!feof($file) ) {
            $line[] = fgetcsv($file, 1024);
        }
        fclose($file);
        return $line;
    }

    $csv_array = readCsv($csv);
    foreach($csv_array as $csvline):
        $teams[] = $csvline[0];
    endforeach;
}

$scores = array();
foreach ($teams as $line) {
    $csvFile = 'scores//dataset-6-random-score-'. $line . '.csv';
    if (file_exists($csvFile)) {
        $csv_score = readCsv($csvFile);
        foreach($csv_score as $csvline):
            $scores[] = [
                'TeamDom' => $csvline[0],
                'TeamExt' => $csvline[1],
                'ButsDom' => (int) $csvline[2],
                'ButsExt' => (int) $csvline[3],
            ];
        endforeach;
    }
}


foreach ($scores as $key => $value) {
    if($value['ButsDom'] > $value['ButsExt']){
        $points[$value['TeamDom']][] = 3;
        $victoires[$value['TeamDom']][] = 1;
        $defaite[$value['TeamExt']][] = 1;
    } elseif($value['ButsExt'] > $value['ButsDom']) {
        $points[$value['TeamExt']][] = 3;
        $victoires[$value['TeamExt']][] = 1;
        $defaite[$value['TeamDom']][] = 1;
    } elseif($value['ButsDom'] == $value['ButsExt']){
        $points[$value['TeamDom']][] = 1;
        $points[$value['TeamExt']][] = 1;
        // Matchs nul
        $nul[$value['TeamDom']][] = 1;
        $nul[$value['TeamExt']][] = 1;
    }
}




// Buts Mis
foreach ($scores as $key => $value) {
    $butMis[$value['TeamDom']][] = (int) $value['ButsDom'];
    $butMis[$value['TeamExt']][] = (int) $value['ButsExt'];
}

// c.Buts Pris
foreach ($scores as $key => $value) {
    $butPris[$value['TeamDom']][] = (int) $value['ButsExt'];
    $butPris[$value['TeamExt']][] = (int) $value['ButsDom'];
}

foreach ($teams as $line) {
    $buts[] =
    [
        'Team' => $line,
        'Buts Mis' => array_sum($butMis[$line]),
        'Buts Pris' => array_sum($butPris[$line]),
    ];
}

// Difference
foreach ($buts as $value) {
    $difference[$value['Team']][] = (int) $value['Buts Mis'] - (int) $value['Buts Pris'];
}



foreach ($teams as $line) {
    $classement[] =
    [
        'Team' => $line,
        'Points' => array_sum($points[$line]),
        'Victoires' => array_sum($victoires[$line]),
        'Nuls' => array_sum($nul[$line]),
        'Defaites' => array_sum($defaite[$line]),
        'Buts Mis' => array_sum($butMis[$line]),
        'Buts Pris' => array_sum($butPris[$line]),
        'Difference' => array_sum($difference[$line])
    ];
}

$keys = array_column($classement, 'Points');
array_multisort($keys, SORT_DESC, $classement);


if (!empty($classement)) {
    if ($f = @fopen("classement" . '.csv', 'w')) {
        fwrite($f, "Team,Points,Victoires,Nuls,Defaites,Buts Mis,Buts Pris,Difference\r\n"); //Entete
        foreach ($classement as $teamligne) {
            fputcsv($f, $teamligne, ",");
        }
        fclose($f);
    }
    echo "Classement crée"  . '</br>';
} else {
    echo "Impossible d'écrire dans le fichier.";
}