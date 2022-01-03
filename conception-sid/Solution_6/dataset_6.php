<?php

// --- Parse des fichiers et récuperation des données de la BDD
$csv = 'dataset-6_files//teams.csv';
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
        $tab_teams[] = $csvline[0];
    endforeach;
}

$tab_equipe_score = array();
foreach ($tab_teams as $line) {
    $csvFile = 'dataset-6_files//dataset-6-random-score-'. $line . '.csv';
    if (file_exists($csvFile)) {
        $csv_array_equipe_score = readCsv($csvFile);
        foreach($csv_array_equipe_score as $csvline):
            $tab_equipe_score[] = [
                'team_domicile' => $csvline[0],
                'point_domicile' => (int) $csvline[2],
                'team_exterieur' => $csvline[1],
                'point_exterieur' => (int) $csvline[3],
            ];
        endforeach;
    }
}

//Pts : Gagne 3 points si gagné , si null gagne 1 point
foreach ($tab_equipe_score as $key => $value) {
    if($value['point_domicile'] > $value['point_exterieur']){
        $tab_teams_Pts[$value['team_domicile']][] = 3;
    } elseif($value['point_exterieur'] > $value['point_domicile']) {
        $tab_teams_Pts[$value['team_exterieur']][] = 3;
    } elseif($value['point_domicile'] == $value['point_exterieur']){
        $tab_teams_Pts[$value['team_domicile']][] = 1;
        $tab_teams_Pts[$value['team_exterieur']][] = 1;
    }
}

// G. = Faire le cumul des matches gagnées par combo d'équipe
foreach ($tab_equipe_score as $key => $value) {
    if($value['point_domicile'] > $value['point_exterieur']){
        $tab_teams_G[$value['team_domicile']][] = 1;
    } elseif($value['point_domicile'] < $value['point_exterieur']) {
        $tab_teams_G[$value['team_exterieur']][] = 1;
    }
}

//N. : Nombre de matchs nuls
foreach ($tab_equipe_score as $key => $value) {
    if($value['point_domicile'] == $value['point_exterieur']){
        $tab_teams_N[$value['team_domicile']][] = 1;
        $tab_teams_N[$value['team_exterieur']][] = 1;
    }
}

//P. : Nombre de matchs perdus
foreach ($tab_equipe_score as $key => $value) {
    if($value['point_domicile'] < $value['point_exterieur']){
        $tab_teams_P[$value['team_domicile']][] = 1;
    } elseif($value['point_domicile'] > $value['point_exterieur']) {
        $tab_teams_P[$value['team_exterieur']][] = 1;
    }
}

// p. = Faire le cumul des points gagnées par combo d'équipe
foreach ($tab_equipe_score as $key => $value) {
    $tab_teams_p[$value['team_domicile']][] = (int) $value['point_domicile'];
    $tab_teams_p[$value['team_exterieur']][] = (int) $value['point_exterieur'];
}

// c. : Nombre de buts encaissés depuis le début du championnat
foreach ($tab_equipe_score as $key => $value) {
    $tab_teams_c[$value['team_domicile']][] = (int) $value['point_exterieur'];
    $tab_teams_c[$value['team_exterieur']][] = (int) $value['point_domicile'];
}

// tab_pret
foreach ($tab_teams as $line) {
    $tab_pret[] =
    [
        'Team' => $line,
        'p.' => array_sum($tab_teams_p[$line]),
        'c.' => array_sum($tab_teams_c[$line]),
    ];
}

// Diff. : p-c
foreach ($tab_pret as $value) {
    $tab_teams_Diff[$value['Team']][] = (int) $value['p.'] - (int) $value['c.'];
}

foreach ($tab_teams as $line) {
    $tab_final[] =
    [
        'Team' => $line,
        'Pts' => array_sum($tab_teams_Pts[$line]),
        'G.' => array_sum($tab_teams_G[$line]),
        'N.' => array_sum($tab_teams_N[$line]),
        'P.' => array_sum($tab_teams_P[$line]),
        'p.' => array_sum($tab_teams_p[$line]),
        'c.' => array_sum($tab_teams_c[$line]),
        'Diff.' => array_sum($tab_teams_Diff[$line])
    ];
}

$keys = array_column($tab_final, 'Pts');
array_multisort($keys, SORT_DESC, $tab_final);


if (!empty($tab_final)) {
    if ($f = @fopen("ranking" . '.csv', 'w')) {
        fwrite($f, "rank,team,Pts,G.,N.,P.,p.,c.,Diff. \r\n"); //Entete
        foreach ($tab_final as $ligne) {
            fputcsv($f, $ligne, ",");
        }
        fclose($f);
    }
    echo "Create ranking" . '.csv' . '</br>';
} else {
    echo "Impossible d'écrire dans le fichier.";
}