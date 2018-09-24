<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <title>fonctions d'accés aux bases de données </title>
  </head>
  <body>
<?php
// fonction principale -------------------------------------------------------------------
include 'pdo_oracle.php';
include 'util_affichage.php';

$mot = $_GET['val'];
$conn = oci_connect('ETU2_36', 'ETU2_36','spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr');
//$conn = OuvrirConnexion('ETU000', 'ETU000','127.0.0.1:1521/xe');
$req = "SELECT nom, prenom FROM prof.vt_coureur where nom like upper('".$mot."%')"; 
//$req = 'SELECT nom FROM prof.vt_coureur';
//$cur = PreparerRequeteOCI($conn,$req);
//$res = ExecuterRequeteOCI($cur);
$nb = LireDonnees1($conn,$req,$donnees);
AfficherDonnee2($donnees,$nb);
//FermerConnexionOCI($conn);
//---------------------------------------------------------------------------------------------
