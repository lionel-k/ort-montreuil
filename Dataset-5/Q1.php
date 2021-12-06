<?php

function readCsv($csv){
    $file = fopen($csv, 'r');
    while (!feof($file) ) {
        $line[] = fgetcsv($file, 1024);
    }
    fclose($file);
    return $line;
}



$csv = 'dataset-5.csv';
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



$lst_tab = array_merge($lstDb);


$separateur = array('-', '|');
$genre_femme = array('F','Fem', 'fem', 'Female');
$genre_male = array('M', 'Masc', 'masc', 'Male');

foreach($lst_tab as $lst):

    $lst['genre'] = str_replace($genre_femme, "F", $lst['genre']);
    $lst['genre'] = str_replace($genre_male, "M", $lst['genre']);
    $location = str_replace($separateur, ';', $lst['location']);
    $location = str_replace(" ", "", $location);
    $transport = str_replace($separateur, ';', $lst['transportation_modes']);
    $transport =  str_replace(" ", "", $transport);

    $lst_format[] = 
    [
        'arrival_date' => date('m/d/Y', strtotime($lst['arrival_date'])),
        'departure_date' => date('d/m/Y', strtotime($lst['departure_date'])),
        'location' => $location,
        'transportation_modes' => $transport,
        'genre' => $lst['genre'],
        'hotel' => $lst['hotel'],
    ];

endforeach;

// recuperer tous les moyens de transports par pays 

foreach($lst_format as $lst):
    $location = explode(";", $lst['location']);
    if(isset($location[1])):
        $pays = $location[1];

        $lst_transports = explode(";", $lst['transportation_modes']);
        foreach($lst_transports as $transport):
            $lst_pays_transport[] = 
            [
                "pays" => $pays,
                "transport" => $transport,
            ];
        endforeach;
    endif;
endforeach;

// // ensuite pour chaque pays, on creer un fichier et on compte le nombre de transport
array_multisort($lst_pays_transport);
$tab_count = array();
$old_pays ="";
$old_trans ="";
$CSVFileName_old="";
$i = 0;
$j=0;
foreach($lst_pays_transport as $pays_trans):
    $pays_transport_str =  str_replace(" ", "", $pays_trans['transport']);
    $pays_str =  str_replace(" ", "", $pays_trans['pays']);
    $CSVFileName = "transportation-modes-".$pays_str.".csv";
    
    if($pays_str == $old_pays)
    {        
        $j++;
        if($pays_transport_str == $old_trans)
        {                     
            $i++;
            $tab_count = array();
            $tab_count[]=
            [
                // "pays" => $pays_str,
                "transport" => $pays_transport_str,
                "nombre" => $i
            ];
        }else{                 
            $fp = fopen($CSVFileName, "a");        
            foreach($tab_count as $data){
                fputcsv($fp, $data);
            }
            fclose($fp);         
            $i = 1;
            $tab_count[] =
            [
                // "pays" => $pays_str,
                "transport" => $pays_transport_str,
                "nombre" => $i
            ]; 
        } 
    }else{ 
        if($CSVFileName_old != "")
        {
            $fp = fopen($CSVFileName_old, "a");  
            foreach($tab_count as $data){
                fputcsv($fp, $data);
            }
            fclose($fp);  
        }       
             
        if($old_pays != "")
        {
            $i = 1;
            $tab_count = array();
    
            $tab_count[] =
            [
                // "pays" => $pays_str,
                "transport" => $pays_transport_str,
                "nombre" => $i
            ];
        }
    }
   
    $old_pays = $pays_str ;
    $old_trans = $pays_transport_str;
    $CSVFileName_old = $CSVFileName;
endforeach;
?>