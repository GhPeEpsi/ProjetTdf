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

    //PROGRAMME PRINCIPAL :
	$annee;
	if (isset($_POST['verifier'])) {
		if (isset($_POST['annee']) && $_POST['annee'] != "pasBon") {
			$annee = $_POST['annee'];
		}
	}

	//REQUETE :
	$reqLigne = 'select rang, code_pays, nom, prenom, temps from tdf_classements_generaux where annee = :annee order by rang';
	$curLigne = preparerRequete($conn,$reqLigne);
    
    //affichage du tableau :
	function affichageClassement() {
		global $conn, $annee, $curLigne;

		//echo "<h1>Classement général du Tour de France ".$annee"</h1>";
		
		//affichage du tableau quoi qu'il ce passe :
		$style = "style=\"border: 1px solid black;\"";
		echo "<table $style>";
		echo "<tr $style>
				<th $style>Rang</th>
				<th $style>Nation</th>
				<th $style>Coureur</th>
				<th $style>Temps</th>
			</tr>";
		
		//affichage des données si une annee est entrée :
		if (isset($annee)) {
			//ajout de l'année aux requetes :
			ajouterParam($curLigne,':annee',$annee);
			
			//résultat :
			$nbLignes = LireDonneesPreparees($curLigne, $tabRes);

			//boucle d'affichage :
			$j =0; //parcour du tableau de resultat
			//afficheLigneTableau($tabNb, $style);
			for ($i = 0; $i < $nbLignes; $i++) {
					afficheLigneTableau($tabRes, $i, $style);
				}
			}
		}
		else {
			echo "</table>";
			echo "<p>Pas encore d'année selectionné !</p>";
		}
	}

	function afficheLigneTableau($tab, $i, $style) {
		
		echo '<tr '.$style.'>
			<th '.$style.'>'.$tab[$i]['RANG'].'</th>
			<th '.$style.'>'.$tab[$i]['CODE_PAYS'].'</th>
			<th '.$style.'>'.utf8_encode($tab[0]['NOM']). ' ' . utf8_encode($tab[0]['PRENOM']).'</th>
			<th '.$style.'>'.$tab[$i]['TEMPS'].'</th>
			</tr>';
	}

	//LE FICHIER HTML:
	include("../html/affichageClassement.html");

?>