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

	$req = 'select distinct nom from tdf_etape, tdf_nation where code_cio = code_cio_d and code_cio_d not like \'FRA\' union(select distinct nom from tdf_etape, tdf_nation where code_cio = code_cio_a and code_cio_a not like \'FRA\' ) order by nom';
	$nbLignes = LireDonnees1($conn, $req, $tab);
	
	//$style = "style=\"border: 1px solid black;\"";
	echo "<h4> Pays visités depuis la création du tour de France :  </h4>";
	echo "<ul>";
	
	for($i=0; $i < $nbLignes; $i++) {
		echo '<li>'.$tab[$i]['NOM'].'</li>';
	}
	echo "</ul>";	
?>