<?php
include './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

//Read the CSV File of teams
$reader = new Csv();
$spreadsheet = $reader->load("dataset-6/football/teams.csv");

$tblTeams = $spreadsheet->getActiveSheet()->toArray();

$tblAllScores = [];
$tblClassement = [];
$tblRank = [];


foreach ($tblTeams as $key => $nomTeam) {

    $tblScore = [];

    $spreadsheet = $reader->load("dataset-6/football/dataset-6-random-score-" . $nomTeam[0] . ".csv");

    $tblScore = $spreadsheet->getActiveSheet()->toArray();
    $tblAllScores[$nomTeam[0]] = $tblScore;
}


foreach ($tblTeams as $key => $nomTeam) {

    $pointTotal = 0;
    $nbWin = 0;
    $nbNul = 0;
    $nbLose = 0;
    $nbButWin = 0;
    $nbButLose = 0;

    foreach ($tblAllScores[$nomTeam[0]] as $key2 => $score) {

        $nbButWin = $nbButWin + $score[2];
        $nbButLose = $nbButLose + $score[3];

        $point = 0;

        if ($score[2] > $score[3]) {
            $nbWin = $nbWin + 1;
            $point = 3;
        } elseif ($score[2] == $score[3]) {
            $nbNul = $nbNul + 1;
            $point = 1;
        } else {
            $nbLose = $nbLose + 1;
        }

        $pointTotal = $pointTotal + $point;

        $butDiff = $nbButWin - $nbButLose;
    }

    $ligneClassement = array(0, $nomTeam[0], $pointTotal, $nbWin, $nbNul, $nbLose, $nbButWin, $nbButLose, $butDiff);


    $tblClassement[] = $ligneClassement;
}


//Trie du classement par le rang
foreach ($tblClassement as $key => $row) {
    $tblRank[$key] = $row[2];
}
array_multisort($tblRank, SORT_DESC, $tblClassement);


//Mettre à jour le classement
$rank = 1;
foreach ($tblClassement as $key => $value) {
    $tblClassement[$key][0] = $rank;

    $rank++;
}


// Ecrire les données : football :
$spreadSheetWriter = new Spreadsheet();

$sheet = $spreadSheetWriter->getActiveSheet();
$spreadSheetWriter->getDefaultStyle()
    ->getFont()
    ->setName('Arial')
    ->setSize(12);

// Coordonnées cellules
$lettreA  = 'A';
$chiffre = '2';
$celluleA = $lettreA . $chiffre;

$spreadSheetWriter->getActiveSheet()
    ->setCellValue("A1", "rank,team,Pts,G.,N.,P.,p.,c.,Diff.");

foreach ($tblClassement as $key => $value) {

    $spreadSheetWriter->getActiveSheet()->getRowDimension($chiffre)->setRowHeight(20);

    $spreadSheetWriter->getActiveSheet()
        ->setCellValue("$celluleA", $value[0] . "," . $value[1] . "," . $value[2] . "," . $value[3] . "," . $value[4] . "," . $value[5] . "," . $value[6] . "," . $value[7] . "," . $value[8]);


    //on passe au chiffre suivant et on l'applique à la position cellule
    $chiffre = $chiffre + 1;
    $celluleA = $lettreA . $chiffre;
}

$nomFichier = "rank-teams";

$writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadSheetWriter);
$writer->save("question-football/" . $nomFichier . ".csv");
