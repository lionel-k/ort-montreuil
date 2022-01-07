<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
	<title>DataTables example - Zero configuration</title>
	<link rel="shortcut icon" type="image/png" href="/media/images/favicon.png">
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="http://www.datatables.net/rss.xml">
	<link rel="stylesheet" type="text/css" href="/media/css/site-examples.css?_=94461d89946ef749b7a43d14685c725d1">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">

	<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="../resources/demo.js"></script>
	<script type="text/javascript" class="init">
	

$(document).ready(function() {
	$('#example').DataTable();
} );


	</script>
</head>

<?php


$servername = "localhost";
$username = "root2";
$password = "";
$dbname = "Dataset5";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);


$Json = file_get_contents("DATAS/dataset-5-json.json");
// Converts to an array 
$myarray = json_decode($Json, true);

$xml = simplexml_load_string('DATAS/dataset-5-xml.xml');
$json = json_encode($xml);
$myarray2 = json_decode($json, TRUE);			  

$xml = simplexml_load_string('DATAS/dataset-5-db.db copie.xml');
$json = json_encode($xml);
$myarray3 = json_decode($json, TRUE);



echo "<pre>";
//print_r($myarray);
echo "</pre>";

//------------ PARSE GENRE -------------
foreach ($myarray as $myarrayS) {




			} 

			 $sql = "INSERT INTO tourism (arrival_date, departure_date, location, transportation_modes, genre, hotel)
			VALUES ('{$myarrayS['arrival_date']}', '{$myarrayS['departure_date']}', '{$myarrayS['location']}','{$myarrayS['transportation_modes']}', '{$myarrayS['genre']}', '{$myarrayS['hotel']}')";
			if ($conn->query($sql) === TRUE) {
			  //echo "New record created successfully";
			} else {
			  //echo "Error: " . $sql . "<br>" . $conn->error;
			}

			$conn->close();
   



					
					
			}

foreach ($myarray2['row'] as $myarrayS) {




			} 

			 $sql = "INSERT INTO tourism (arrival_date, departure_date, location, transportation_modes, genre, hotel)
			VALUES ('{$myarrayS['arrival_date']}', '{$myarrayS['departure_date']}', '{$myarrayS['location']}','{$myarrayS['transportation_modes']}', '{$myarrayS['genre']}', '{$myarrayS['hotel']}')";
			if ($conn->query($sql) === TRUE) {
			  //echo "New record created successfully";
			} else {
			  //echo "Error: " . $sql . "<br>" . $conn->error;
			}




foreach ($myarray3['row'] as $myarrayS) {




			} 

			 $sql = "INSERT INTO tourism (arrival_date, departure_date, location, transportation_modes, genre, hotel)
			VALUES ('{$myarrayS['arrival_date']}', '{$myarrayS['departure_date']}', '{$myarrayS['location']}','{$myarrayS['transportation_modes']}', '{$myarrayS['genre']}', '{$myarrayS['hotel']}')";
			if ($conn->query($sql) === TRUE) {
			  //echo "New record created successfully";
			} else {
			  //echo "Error: " . $sql . "<br>" . $conn->error;
			}

			$conn->close();
   
			$conn->close();
   



					
					
			}
			
			
			



		
		
			
  