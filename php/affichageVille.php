<?php
	
	//On inclut les fichiers nécéssaire.
	include ("pdo_oracle.php");
	include ("verificationsForm.php");
	include ("../html/navBar.html");

	/* --------------------------------------------------------------------------------------------------------------------------------- */
	/* ----------------------------------------------------Connexion-------------------------------------------------------------------- */
	/* --------------------------------------------------------------------------------------------------------------------------------- */


	 $db_username = 'ETU2_49';
	 $db_password = 'ETU2_49';
	 $db = "oci:dbname=spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr;charset=AL32UTF8";


	//connexion locale de Jérémy
	// $db_username = 'copie_tdf_copie';
	// $db_password = 'copie_tdf_copie';
	// $db = "oci:dbname=localhost:1521/xe;charset=AL32UTF8";
	//$db = fabriquerChaineConnexion();

	$conn = OuvrirConnexion($db,$db_username,$db_password);

	//Les deux requetes qui suivent permettent de selectionner dans la base les villes de depart et d'arrivée
	$req = 'select distinct ville_d from tdf_etape order by ville_d ';
	$req1 = 'select distinct ville_a from tdf_etape order by ville_a';
	$nbLignes = LireDonnees1($conn, $req, $tab);
	$nbLignes1 = LireDonnees1($conn, $req1, $tab1);
	
	//On les affiche ensuite avec du HTML en ajoutant un petit style css

	echo "<h4> Ville étapes depuis la création du tour de France :  </h4>";
	echo '<table style="margin :auto">';
	echo "<tr>
	<th style= \"border: 1px solid black;\" >Ville de départ</th>
	<th style= \"border: 1px solid black;\">Ville d'arrivée</th></tr>";


	if($nbLignes <= $nbLignes1 ){
		for($i=0; $i < $nbLignes1; $i++) {
			echo "<tr>";
			echo '<th style= "border: 1px solid black;">'.$tab1[$i]['VILLE_D'].'</th>';
			if (isset($tab[$i]['VILLE_A'])) {
				echo '<th style= "border: 1px solid black;">'.$tab[$i]['VILLE_A'].'</th>';
			}else{
				echo '<th style= "border: 1px solid black;"> </th>';
			}
			echo "</tr>";
		}
	}else{
		for($i=0; $i < $nbLignes; $i++) {
			echo "<tr>";
			echo '<th style= "border: 1px solid black;">'.$tab[$i]['VILLE_D'].'</th>';
			if (isset($tab1[$i]['VILLE_A'])) {
				echo '<th style= "border: 1px solid black;">'.$tab1[$i]['VILLE_A'].'</th>';
			}else{
				echo '<th style= "border: 1px solid black;"> </th>';
			}
			echo "</tr>";
		}
	}
		
	echo "</table>";
	
?>