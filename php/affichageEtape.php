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
	$reqLigne = 'select n_epreuve, nom, prenom, heure, minute, seconde, total_seconde, distance, jour from tdf_etape
				join tdf_temps using (annee, n_epreuve)
				join tdf_coureur using (n_coureur)
				where annee = :annee
				and rang_arrivee = 1
				order by n_epreuve';
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
	
	//affichage du tableau :
	function affichage() {
		global $conn, $annee, $curLigne, $curNbEtape;
		
		//affichage du tableau quoi qu'il ce passe :
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
		
		//affichage des données si une annee est entrée :
		if (isset($annee)) {
			//ajout de l'année à la requete d'infos d'étape :
			ajouterParam($curLigne,':annee',$annee);
			$nbEpreuve = LireDonneesPreparees($curLigne, $tab);
			
			//detection de plusieur vainqueur par etape :
			$tmp=-150; //sert a garder en memoire le n_epreuve précédent pour savoir s'il y a plusieur gagnant par étape :
			$tabEpMultiGagn;
			foreach ($tab as $epreuve) {
				if ($tmp == $epreuve['N_EPREUVE']) {
					$tabEpMultiGagn[]=$epreuve['N_EPREUVE'];
				}
				$tmp = $epreuve['N_EPREUVE'];
			}

			//preparation des dernier parametre plus execution de la requette et affichage :
			$j=0;//sert a parcourir tabEpMultiGagn
			for ($i = $tab[0]['N_EPREUVE'] ; $i<$nbEpreuve ; $i++) {
				$epreuve = array();
				if (isset($tabEpMultiGagn[$j]) && ($tab[$i]['N_EPREUVE'] == $tabEpMultiGagn[$j])) { //il faut que si n_epreuve est save dans le tableau alorson envoi un tableau complet
					$epreuve[] = $tab[$i];
					
					if (!isset($tabEpMultiGagn[$j+1]) || ($tabEpMultiGagn[$j] != $tabEpMultiGagn[$j+1]))
						afficheLigneTableau($epreuve, $style);
					$j++;
				}
				else {
					$epreuve = $tab[$i];
					afficheLigneTableau($epreuve, $style);
				}
			}
			
			echo "</table>";
		}
		else {
			echo "</table>";
			echo "<p>Pas encore d'année selectionné !</p>";
		}
	}
	
	function afficheLigneTableau($tab, $style) {
		print_r($tab);
		echo '<br><br><br><br><br><br><br>';
		
		
		
		
		
		/*echo '<tr '.$style.'>
				<th '.$style.'>'.$tab['N_EPREUVE'].'</th>
				<th '.$style.'>'.$tab['DISTANCE'].'</th>
				<th '.$style.'>'.$tab['JOUR'].'</th>
				<th '.$style.'>'.utf8_encode($tab['NOM']). ' ' . utf8_encode($tab['PRENOM']).'</th>
				<th '.$style.'>'.$tab['HEURE']. 'h/' .$tab['MINUTE']. 'min/' .$tab['SECONDE'].'s</th>
				<th '.$style.'>'.$tab['TOTAL_SECONDE'].'</th>
			</tr>';*/
	}


	

	//LE FICHIER HTML:
	include("../html/affichageEtape.html");

?>
