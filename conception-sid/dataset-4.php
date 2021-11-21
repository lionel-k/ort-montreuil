<?php

// Liste 1 <= Liste des licences : ingrédients qui sont déjà vendu , par hôtels
$liste1 = array();

// a. Parse fichier CSV
$csv = 'dataset-4-csv.csv';
if (file_exists($csv)) {
    function readCsv($csv) {
        $file = fopen($csv, 'r');
        while (!feof($file)) 
        {
            $line[] = fgetcsv($file, 1024); 
        }
        fclose($file);
        return $line;
    }

    $csv_array = readCsv($csv);

    $tab_csv = array();
    foreach($csv_array as $csv):
        $tab_csv[] =[
            'ingredient_name' => $csv[2],
            'hotel_name' => $csv[1],
        ];
    endforeach;

    $tab_final_csv = array();
    foreach($tab_csv as $lineCsv):
        $unIngredient = explode(",", $lineCsv["ingredient_name"]);
        foreach ($unIngredient as $ing):
            $tab_final_csv[] =[
                'ingredient_name' => $ing,
                'hotel_name' => $lineCsv["hotel_name"],
            ];
        endforeach;
    endforeach;
}
// b. Parse fichier JSON
$json = 'dataset-4-json.json';
if (file_exists($json)) {
    function readJson($json){
        $data = file_get_contents($json);
        $obj = json_decode($data);
        return $obj;
    }

    $json_array = readJson($json);

    foreach($json_array as $json):
        $tab_json[] = [
            'ingredient_name' => $json->ingredients,
            'hotel_name' => $json->hotel_name,];
    endforeach;

    $tab_final_json = array();
    foreach($tab_json as $lineJson):
        $unIngredient = explode(",", $lineJson["ingredient_name"]);
        foreach ($unIngredient as $ing):
            $tab_final_json[] =[
                'ingredient_name' => $ing,
                'hotel_name' => $lineJson["hotel_name"],
            ];
        endforeach;
    endforeach;
}
//c. Parse fichier XML
$xml = 'dataset-4-xml.xml';
if (file_exists($xml)) {
    $xml_array = simplexml_load_file($xml);
    foreach($xml_array as $xml):
        $tab_xml[] = [
            'ingredient_name' => $xml->ingredients,
            'hotel_name' => $xml->hotel_name,];
    endforeach;
    $tab_final_xml = array();
    foreach($tab_xml as $linexml):
        $unIngredient = explode(",", $linexml["ingredient_name"]);
        foreach ($unIngredient as $ing):
            $tab_final_xml[] =[
                'ingredient_name' => $ing,
                'hotel_name' => $linexml["hotel_name"],
            ];
        endforeach;
    endforeach;
}

if (!empty($tab_final_csv) && !empty($tab_final_csv) && !empty($tab_final_xml)) {
    $liste1 = array_merge($tab_final_csv, $tab_final_json, $tab_final_xml);
}


//Liste d’hôtels et de recettes (Liste 2)

//Define PDO - tel about the database file
$pdo = new PDO('sqlite:leads.db');

//Write SQL
$statement = $pdo->query("SELECT d.hotel_code, hotel_name, d.dish_code, dish_name, ingredient_name FROM dishes as d, hotels as h, ingredients i
where d.hotel_code =  h.hotel_code
and d.dish_code = i.dish_code
order by hotel_name");

//Run the SQL
$liste2 = $statement->fetchAll(PDO::FETCH_ASSOC);

//Compare diff in the 2 lists

// Récuperer liste des ingrédients certifié unique
$tabTempIngUnique = array();
foreach ($liste1 as $ing) {
    $tabTempIngUnique[] = $ing['ingredient_name'];
}
$tabIngCertifUnique = array_unique($tabTempIngUnique);

// Récuperer liste des hotels unique
$tabTempHotelUnique = array();
foreach ($liste1 as $hotel) {
    $tabTempHotelUnique[] = $hotel['hotel_name'];
}
$tabHotelUnique = array_unique($tabTempHotelUnique);   


$liste3 = array();
foreach ($liste2 as $bdd) {
    //Vérifier si hôtel (bdd) corresp/existe avec un autre hôtel des fichiers 
    // -- Si hôtel existe déjà dans la liste des contrats
    if (in_array($bdd['hotel_name'], $tabHotelUnique)) {
        // -- Si ingrédients de l'hotel n'existe pas dans fichiers alors
        // On recherche si l'hôtel à déjà un contrat avec l'ingredrient
        $findKeyListe1 = search_revisions($liste1, $bdd['ingredient_name'], 'ingredient_name', $bdd['hotel_name'], 'hotel_name');
        if (count($findKeyListe1) <= 0) {
            // -- Si la combinaision hotel + ingredient n'existe pas alors   
            // Vérifier que l'ingrédient (bdd) existe dans la liste des ingrédients cerfifiés
            if (in_array($bdd['ingredient_name'], $tabIngCertifUnique)) {
                $liste3[] =[
                    'ingredient_name' => $bdd['ingredient_name'],
                    'hotel_name' => $bdd['hotel_name'],
                    'dish_name' => $bdd['dish_name'],
                ];
                $liste4[] =[
                    'ingredient_name' => $bdd['ingredient_name'],
                    'hotel_name' => $bdd['hotel_name'],
                ];
            }
        }
    } else {
    // -- S'il n'existe pas alors : nouveau hotel    
        if (in_array($bdd['ingredient_name'], $tabIngCertifUnique)) {
            $liste3[] =[
                'ingredient_name' => $bdd['ingredient_name'],
                'hotel_name' => $bdd['hotel_name'],
                'dish_name' => $bdd['dish_name'],
            ];
            $liste4[] =[
                'ingredient_name' => $bdd['ingredient_name'],
                'hotel_name' => $bdd['hotel_name'],
            ];
        }
    }
}

//Ecrire le resultat dans un fichier csv
if ($f = @fopen('resultat.csv', 'w')) {
    foreach ($liste3 as $ligne) {
      fputcsv($f, $ligne, ";");
      }
    fclose($f);
    }
  else {
    echo "Impossible d'écrire dans le fichier.";
}

// Enlever les doublons
function array_unique_multidimensional($input)
{
    $serialized = array_map('serialize', $input);
    $unique = array_unique($serialized);
    return array_intersect_key($input, $unique);
}
$liste4 = array_unique_multidimensional($liste4);

if ($f = @fopen('resultat_SansDoublons.csv', 'w')) {
    foreach ($liste4 as $ligne) {
      fputcsv($f, $ligne, ";");
      }
    fclose($f);
    }
  else {
    echo "Impossible d'écrire dans le fichier.";
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datatable 4</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.19.1/dist/bootstrap-table.min.css"
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
    <div class="row">
        <?php if (count($liste1) > 0): ?>
        <div class="col-6">
            <h3>Liste 1 (Fichiers) : Contrats </h3>
            Que vous avez déjà réalisés ainsi que les ingredients que vous avez déjà vendus
            <table id="dtBasicExample1" class="table table-striped" data-toggle="table" data-pagination="true" data-search="true" data-row-style="rowStyle">
            <thead class="thead-dark">
            <tr>
                <th data-sortable="true" scope="col">ID</th>
                <th data-sortable="true" scope="col">Ingrédients</th>
                <th data-sortable="true" scope="col">Hôtels</th>
            </tr>
            </thead>
            <tbody>
            <?php for ($i = 1; $i < count($liste1); $i++){?>
            <tr>
                <th scope="row"><?php echo $i; ?></th>
                <td><?php echo $liste1[$i]["ingredient_name"]; ?></td>
                <td><?php echo $liste1[$i]["hotel_name"]; ?></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
        <?php if (count($liste2) > 0): ?>
        <div class="col-6">
        <h3>Liste 2 (BDD) : Potentiels hôtels </h3>
        Que vous pouvez contactez en vue de leur vendre des ingrédients que vous avez vendu à d'autres hotels
        <table id="dtBasicExample2" class="table table-striped" data-toggle="table" data-pagination="true" data-search="true" data-row-style="rowStyle">
        <thead class="thead-light">
        <tr>
            <th data-sortable="true" scope="col">ID</th>
            <th data-sortable="true" scope="col">Ingredients</th>
            <th data-sortable="true" scope="col">Hôtels
        </tr>
        </thead>
        <tbody>
        <?php for ($i = 1; $i < count($liste2); $i++){?>
        <tr>
            <th scope="row"><?php echo $i; ?></th>
            <td><?php echo $liste2[$i]["ingredient_name"]; ?></td>
            <td><?php echo $liste2[$i]["hotel_name"]; ?></td>
        </tr>
        <?php } ?>
        </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
    <div class="row">
        <?php if (count($liste3) > 0): ?>
        <div class="col-6">
            <h3>Liste 3 : Résultats </h3>
            <table id="dtBasicExample3" class="table table-striped" data-toggle="table" data-pagination="true" data-search="true" data-row-style="rowStyle">
            <thead class="thead-light">
            <tr>
                <th data-sortable="true" scope="col">ID</th>
                <th data-sortable="true" scope="col">Ingredients</th>
                <th data-sortable="true" scope="col">Hôtels
            </th>
            </tr>
            </thead>
            <tbody>
            <?php for ($i = 1; $i < count($liste3); $i++){?>
            <tr>
                <th scope="row"><?php echo $i; ?></th>
                <td><?php echo $liste3[$i]["ingredient_name"]; ?></td>
                <td><?php echo $liste3[$i]["hotel_name"]; ?></td>
            </tr>
            <?php } ?>
            </tbody>
            </table>
        </div>
        <?php endif; ?>
        <?php if (count($liste4) > 0): ?>
        <div class="col-6">
            <h3>Liste 4 : Sans doublons </h3>
            <table id="dtBasicExample4" class="table table-striped" data-toggle="table" data-pagination="true" data-search="true" data-row-style="rowStyle">
            <thead class="thead-dark">
            <tr>
                <th data-sortable="true" scope="col">ID</th>
                <th data-sortable="true" scope="col">Ingredients</th>
                <th data-sortable="true" scope="col">Hôtels
            </th>
            </tr>
            </thead>
            <tbody>
            <?php for ($i = 1; $i < count($liste4); $i++){
            if (!empty($liste4[$i]["ingredient_name"])) { ?>
            <tr>
                <th scope="row"><?php echo $i; ?></th>
                <td><?php echo $liste4[$i]["ingredient_name"]; ?></td>
                <td><?php echo $liste4[$i]["hotel_name"]; ?></td>
            </tr>
            <?php } ; } ?>
            </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>    
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://unpkg.com/bootstrap-table@1.19.1/dist/bootstrap-table.min.js"></script>
</body>
</html>
<?php


function search_revisions($dataArray, $search_value, $key_to_search, $other_matching_value = null, $other_matching_key = null) {
    $keys = array();
    foreach ($dataArray as $key => $cur_value) {
        if ($cur_value[$key_to_search] == $search_value) {
            if (isset($other_matching_key) && isset($other_matching_value)) {
                if ($cur_value[$other_matching_key] == $other_matching_value) {
                    $keys[] = $key;
                }
            } else {
                $keys[] = $key;
            }
        }
    }
    return $keys;
}

?>