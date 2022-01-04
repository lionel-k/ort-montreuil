<?php

$csvdir = "./scores/";
$csvcontent = '';
if (is_dir($csvdir)) {
    if ($handle = opendir($csvdir)) {
        while (($file = readdir($handle)) !== false) {
            if (substr($file, -4) === ".csv") {
                $csvcontent .= file_get_contents($csvdir . $file);
            }
        }
        closedir($handle);
    }
}

$result = fopen('./scores.csv', 'w');
fwrite($result, $csvcontent);
fclose($result);


$file = fopen("teams.csv", "r");
while($row = fgetcsv($file)) {
       
   

    $tab_teams[] = [
        $team = $row[0]
    ];

}


   
   
   $file2 = fopen("scores.csv", "r");

   while($row = fgetcsv($file2)) {
       
   

    $scores[] = [
        $t1 = $row[0],
        $t2 = $row[1],
        $s1 = $row[2],
        $s2 = $row[3]
    ];

    
    
    $points = 0;
    $victoires = 0;
    $defaites = 0;
    $nuls = 0;
    $butsMis = 0;
    $butsPris = 0;
    

   



   if ($team = $row[0]) {

    if ($row[2] > $row[3]) {

        $points = 3;
        $victoires = 1;
        $defaites = 0;
        $nuls = 0;
        $butsMis = $row[2];
        $butsPris = $row[3];
    }

    elseif($row[2] == $row[3]){
        $points = 1;
        $victoires = 0;
        $defaites = 0;
        $nuls = 1;
        $butsMis1= $row[2];
        $butsPris = $row[3];

    }

    else{
        $points = 0;
        $victoires = 0;
        $defaites = 1;
        $nuls = 0;
        $butsMis = $row[2];
        $butsPris = $row[3];

    }


   

}

   

    

    if ($team = $row[1]) {

            if ($row[3] > $row[2]) {
        
                $points = 3;
                $victoires = 1;
                $defaites = 0;
                $nuls = 0;
                $butsMis = $row[3];
                $butsPris = $row[2];
                
            }
        
            
        
            elseif($row[3] == $row[2]) {
                $points = 1;
                $victoires = 0;
                $defaites = 0;
                $nuls = 1;
                $butsMis = $row[3];
                $butsPris = $row[2];
        
            }
            else{
                $points = 0;
                $victoires = 0;
                $defaites = 1;
                $nuls = 0;
                $butsMis = $row[3];
                $butsPris = $row[2];
        
            }


      

        
    }


    foreach ($scores as $row) {


        $pointsEquipe[] = [
            "Team" => $team,
            "Points" => count($points),
            "Victoires" => count($victoires),
            "Defaites" => count($defaites),
            "Nuls" => count($nuls),
            "Buts Mis" => count($butsMis),
            "Buts Pris" => count($butsPris)

    
           
        ];

    }

    




    

    
   }




















  
   







