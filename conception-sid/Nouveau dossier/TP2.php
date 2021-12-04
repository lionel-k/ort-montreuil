<?php

// jsonToCSV
function jsonToCSV($jfilename, $cfilename)
{
    if (($json = file_get_contents($jfilename)) == false)
        die('Error reading json file...');
    $data = json_decode($json, true);
    $fp = fopen($cfilename, 'w');
    $header = false;
    foreach ($data as $row)
    {
        if (empty($header))
        {
            $header = array_keys($row);
            fputcsv($fp, $header);
            $header = array_flip($header);
        }
        fputcsv($fp, array_merge($header, $row));
    }
    fclose($fp);
    return;
}


$json_filename = 'dataset-5-json.json';
$csv_filename = 'dataset-5-json.csv';
jsonToCSV($json_filename, $csv_filename);
echo 'Successfully converted json to csv file. <a href="' . $csv_filename . '" target="_blank">Click here to open it.</a>';


// XmlToCsvFile
function convertXmlToCsvFile($xml_file_input, $csv_file_output) {
	$xml = simplexml_load_file($xml_file_input);
	
	$output_file = fopen($csv_file_output, 'w');
	
	$header = false;
	
	foreach($xml as $key => $value){
		if(!$header) {
			fputcsv($output_file, array_keys(get_object_vars($value)));
			$header = true;
		}
		fputcsv($output_file, get_object_vars($value));
	}
	
	fclose($output_file);
}

convertXmlToCsvFile("dataset-5-xml.xml", "dataset-5-xml.csv");






function joinFiles(array $files, $result) {
    if(!is_array($files)) {
        throw new Exception('`$files` must be an array');
    }

    $wH = fopen($result, "w+");

    foreach($files as $file) {
        $fh = fopen($file, "r");
        while(!feof($fh)) {
            fwrite($wH, fgets($fh));
        }
        fclose($fh);
        unset($fh);
        fwrite($wH, "\n"); //usually last line doesn't have a newline
    }
    fclose($wH);
    unset($wH);
}


joinFiles(array('dataset-5-json.csv', 'dataset-5-xml.csv'), 'dataset-5.csv');  






?>