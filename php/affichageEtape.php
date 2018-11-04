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
	
	$reqNbVaiqueur = 'select n_epreuve, count(*) as nb from tdf_etape
					join tdf_temps using (annee, n_epreuve)
					where annee = :annee
					and rang_arrivee = 1
					group by n_epreuve
					order by n_epreuve';
	$curNbVainqueur = preparerRequete($conn,$reqNbVaiqueur);
	
	
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
			if (!empty($annee) && $val == $annee)
				echo '<option value="'.$val.'" selected>'.$val.'</option>';
			else
				echo '<option value="'.$val.'">'.$val.'</option>';
		}
	}
	
	//affichage du tableau :
	function affichage() {
		global $conn, $annee, $curLigne, $curNbVainqueur;
		
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
			//ajout de l'année aux requetes :
			ajouterParam($curLigne,':annee',$annee);
			ajouterParam($curNbVainqueur,':annee',$annee);
			
			//résultat :
			$nbEpreuve = LireDonneesPreparees($curLigne, $tabRes);
			$nbVainqueur = LireDonneesPreparees($curNbVainqueur, $tabNb);

			//boucle d'affichage :
			$j =0; //parcour du tableau de resultat
			//afficheLigneTableau($tabNb, $style);
			foreach ($tabNb as $etape) {
				$tab = array();
				for ($i = 0 ; $i<$etape['NB'] ; $i++) {
					$tab[] = $tabRes[$j];
					$j++;
				}
				afficheLigneTableau($tab, $style);
			}
		}
		else {
			echo "</table>";
			echo "<p>Pas encore d'année selectionné !</p>";
		}
	}
	
	function fabriqueCondition($EpMultiGagn) {
		echo '<h1>coucou1</h1>';
		$condition =
		(
			(
				isset($epreuve[0]['N_EPREUVE'])
				&&
				($epreuve[0]['N_EPREUVE'] != $EpMultiGagn)
			)
			|| 
			(
				isset($epreuve['N_EPREUVE'])
				&&
				($epreuve['N_EPREUVE'] != $EpMultiGagn)
			)
		);
		
		return $condition;
	}
	
	function afficheLigneTableau($tab, $style) {
		/*echo '<pre>';
		print_r($tab);
		echo '</pre><br><br><br><br><br><br><br>';*/
		
		if (!isset($tab[1])) {
			echo '<tr '.$style.'>
				<th '.$style.'>'.$tab[0]['N_EPREUVE'].'</th>
				<th '.$style.'>'.$tab[0]['DISTANCE'].'</th>
				<th '.$style.'>'.$tab[0]['JOUR'].'</th>
				<th '.$style.'>'.utf8_encode($tab[0]['NOM']). ' ' . utf8_encode($tab[0]['PRENOM']).'</th>
				<th '.$style.'>'.$tab[0]['HEURE']. 'h/' .$tab[0]['MINUTE']. 'min/' .$tab[0]['SECONDE'].'s</th>
				<th '.$style.'>'.$tab[0]['TOTAL_SECONDE'].'</th>
				</tr>';
		}
		else {
			echo '<tr '.$style.'>
				<th '.$style.'>'.$tab[0]['N_EPREUVE'].'</th>
				<th '.$style.'>'.$tab[0]['DISTANCE'].'</th>
				<th '.$style.'>'.$tab[0]['JOUR'].'</th>
				<th '.$style.'>'. afficheNomPrenom($tab).'</th>
				<th '.$style.'>'.$tab[0]['HEURE']. 'h/' .$tab[0]['MINUTE']. 'min/' .$tab[0]['SECONDE'].'s</th>
				<th '.$style.'>'.$tab[0]['TOTAL_SECONDE'].'</th>
				</tr>';
		}
	}
	
	function afficheNomPrenom($tab) {
		$retour = '';
		foreach ($tab as $ligne) {
			$retour = $retour . utf8_encode($ligne['NOM']). ' ' . utf8_encode($ligne['PRENOM']).'<br>';
		}

		return $retour;
	}

	//LE FICHIER HTML:
	include("../html/affichageEtape.html");

?>