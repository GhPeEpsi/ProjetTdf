<?php
	
	include ("pdo_oracle.php");
	include ("verificationsForm.php");
	include ("../html/navBar.html");

	$texteFinal = "";
	// connexion à la base
	 $db_username = 'ETU2_49';
	 $db_password = 'ETU2_49';
	 $db = "oci:dbname=spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr;charset=AL32UTF8";


	//connection de Jérémy qui resté là après la merge
	// $db_username = 'copie_tdf_copie';
	// $db_password = 'copie_tdf_copie';
	// $db = "oci:dbname=localhost:1521/xe;charset=AL32UTF8";
	//$db = fabriquerChaineConnexion();


	//$db_username = 'copie_tdf';
	//$db_password = 'copie_tdf';
	//$db = fabriquerChaineConnexion2();
	$conn = OuvrirConnexion($db,$db_username,$db_password);

	$req = 'select distinct ville_d as ville from tdf_etape union ( select distinct ville_a as ville from tdf_etape ) order by ville ';
	LireDonnees1($conn, $req, $tab);
	echo '<pre>';
	//print_r($tab);
	foreach ($tab as $ligne) {
		echo $ligne['VILLE'];
		echo "<br>";
	}

	include("../html/affichageVille.html")
	
?>