<?php

// PARSE FICHIER SQL

try

{
       // On se connecte à MySQL
       $bdd = new PDO('mysql:host=localhost;dbname=bddtp2', 'root', 'root');

}

catch(Exception $e)

{

       // En cas d'erreur, on affiche un message et on arrête tout
       die('Erreur : '.$e->getMessage());

}

       

// Si tout va bien, on peut continuer


// On récupère tout le contenu de la table jeux_video

$reponse = $bdd->query('SELECT * FROM jeux_video');


// On affiche chaque entrée une à une

while ($donnees = $reponse->fetch())

{

?>