<?php
    include ("pdo_oracle.php");
	include ("util_affichage.php");
	include ("../html/navBar.html");

	/*Serveur UNICAEN*/
	$login = 'ETU2_49';
	$mdp = 'ETU2_49';
	$db = "oci:dbname=spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr;charset=AL32UTF8";
	// $db = fabriquerChaineConnexion();
	$conn = OuvrirConnexion($db,$login,$mdp);
	
	
	// $db_username = 'projet_php';
	// $db_password = 'projet_php';
	// $db = fabriquerChaineConnexion2();
	// $conn = OuvrirConnexion($db,$db_username,$db_password);

    //PROGRAMME PRINCIPAL :
	$annee;
	if (isset($_POST['verifier'])) {
		if (isset($_POST['annee']) && $_POST['annee'] != "pasBon") {
			$annee = $_POST['annee'];
		}
	}

	//REQUETE :
	$reqLigne = 'select distinct rang, code_pays, nom, prenom, temps  from tdf_classements_generaux join
tdf_parti_coureur 
using(n_coureur) where tdf_classements_generaux.annee = :annee and valide != \'R\'order by rang';
	$curLigne = preparerRequete($conn,$reqLigne);
    
    //affichage du tableau :
	function affichageClassement() {
		global $conn, $annee, $curLigne;
		
		//affichage des données si une annee est entrée :
		if (isset($annee)) {
			//ajout de l'année aux requetes :
			ajouterParam($curLigne,':annee',$annee);
			
			//résultat :
			$nbLignes = LireDonneesPreparees($curLigne, $tabRes);

			$style = "style=\"border: 1px solid black;\"";
			echo "<table $style>";
			echo "<tr $style>
				<th $style>Rang</th>
				<th $style>Nation</th>
				<th $style>Coureur</th>
				<th $style>Temps</th>
			</tr>";

			//boucle d'affichage :
			foreach ($tabRes as $ligne) {
				//echo "salut";
				afficheLigneTableau($ligne, $style);
			}
			echo "</table>";
		}
		else {
			echo "</table>";
			echo "<p>Pas encore d'année selectionné !</p>";
		}
	}

	function afficheLigneTableau($tab, $style) {
		echo '<tr '.$style.'>
			<th '.$style.'>'.$tab['RANG'].'</th>
			<th '.$style.'>'.$tab['CODE_PAYS'].'</th>
			<th '.$style.'>'.$tab['NOM']. ' ' .$tab['PRENOM'].'</th>
			<th '.$style.'>'.getTemps($tab['TEMPS']).'</th>
			</tr>';
	}

	function getTemps($temps) {
		$heures = floor(intval($temps) / 3600);
		$minutes =  floor((intval($temps) % 3600) / 60);
		$secondes = floor((intval($temps) % 3600) % 60);

		return $heures."h".$minutes."\'".$secondes."\'\'";
	}

	//LE FICHIER HTML:
	include("../html/affichageClassement.html");

?>