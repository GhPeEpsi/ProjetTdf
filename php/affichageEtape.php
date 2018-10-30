<?php
	include ("pdo_oracle.php");
	include ("util_affichage.php");
	include ("../html/navBar.html");

	/*Serveur UNICAEN
	$login = 'ETU2_49';
	$mdp = 'ETU2_49';
	$db = fabriquerChaineConnexion();
	$conn = OuvrirConnexion($db,$login,$mdp);
	*/
	/*Bastien Localhost*/
	$login = 'copie_tdf';
	$mdp = 'copie_tdf';
	$db = fabriquerChaineConnexion2();
	$conn = OuvrirConnexion($db,$login,$mdp);
	
	//PROGRAMME PRINCIPAL :
	$annee;
	if (isset($_POST['verifier'])) {
		if (isset($_POST['annee']) && $_POST['annee'] != "pasBon") {
			$annee = $_POST['annee'];
		}
	}

	//REQUETE :
	$reqNombreEpreuve = 'select ';
	
	$reqLigne = 'select n_epreuve, distance, jour, nom, prenom, total_seconde from tdf_etape
				join tdf_temps using (annee, n_epreuve)
				join tdf_coureur using (n_coureur)
				where annee = :annee
				and n_epreuve = :n_epreuve
				and total_seconde >= all
				(
					select total_seconde from tdf_etape
					join tdf_temps using (annee, n_epreuve)
					join tdf_coureur using (n_coureur)
					where annee = :annee
					and n_epreuve = :n_epreuve
				)
				order by n_epreuve, total_seconde';
	
	
	//FONCTIONS DE TRAITEMENT :
	//Récupération de la liste des années
	function listeAnnee() {
		global $conn, $annee;
		$req = "select annee from tdf_annee order by annee";
		LireDonnees1($conn, $req, $tab);
		
		if (!empty($annee)) {echo '<option value="pasBon">Année</option>';}
		else {echo '<option value="pasBon" selected>Année</option>';}
		
		foreach ($tab as $sousTab) {
			$val = $sousTab['ANNEE'];
			if (!empty($annee) && $val = $annee)
				echo '<option value="'.$val.'" selected>'.$val.'</option>';
			else
				echo '<option value="'.$val.'">'.$val.'</option>';
		}
	}
	
	function affichage() {
		global $annee;
		if (isset($annee)) {
			
		}
	}


	

	//LE FICHIER HTML:
	include("../html/affichageEtape.html");

?>
