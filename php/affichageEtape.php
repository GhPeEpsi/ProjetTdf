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
	$reqNombreEpreuve = 'select n_epreuve from tdf_etape where annee = :annee order by n_epreuve';
	$curNbEtape = preparerRequete($conn,$reqNombreEpreuve);
	
	$reqLigne = 'select n_epreuve, distance, jour, heure, minute, seconde, nom, prenom, total_seconde from tdf_etape
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
	$curLigne = preparerRequete($conn,$reqLigne);
	
	
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
		global $conn, $annee, $curLigne, $curNbEtape;
		
		$style = "style=\"border: 1px solid black;\"";
		echo "<table $style>";
		echo "<tr $style>
				<th $style>N° Epreuve</th>
				<th $style>Distance (en km)</th>
				<th $style>Date</th>
				<th $style>Gagnant</th>
				<th $style>Temps</th>
				<th $style>Temps (en s)</th>
			</tr>";
		
		if (isset($annee)) {
			//récupération du nombre d'étapes :
			ajouterParam($curNbEtape,':annee',$annee);
			$nbEtapes = LireDonneesPreparees($curNbEtape, $tabNb);

			//ajout de l'année à la requete d'infos d'étape :
			ajouterParam($curLigne,':annee',$annee);

			//preparation des dernier parametre plus execution de la requette et affichage :
			foreach ($tabNb as $epreuve) {
				ajouterParam($curLigne,':n_epreuve',$epreuve['N_EPREUVE']);
				LireDonneesPreparees($curLigne, $tab);
				afficheLigneTableau($tab, $style);
				
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
				<th '.$style.'>'.$tab[0]['N_EPREUVE'].'</th>
				<th '.$style.'>'.$tab[0]['DISTANCE'].'</th>
				<th '.$style.'>'.$tab[0]['JOUR'].'</th>
				<th '.$style.'>'.utf8_encode($tab[0]['NOM']). ' ' . utf8_encode($tab[0]['PRENOM']).'</th>
				<th '.$style.'>'. $tab[0]['HEURE']. 'h/' .$tab[0]['MINUTE']. 'min/' .$tab[0]['SECONDE'].'s</th>
				<th '.$style.'>'.$tab[0]['TOTAL_SECONDE'].'</th>
			</tr>';
	}


	

	//LE FICHIER HTML:
	include("../html/affichageEtape.html");

?>
