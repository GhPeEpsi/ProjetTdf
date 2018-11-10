<?php
	include ("pdo_oracle.php");
	include ("util_affichage.php");
	include ("../html/navBar.html");

	/*Serveur UNICAEN*/
	$login = 'ETU2_49';
	$mdp = 'ETU2_49';
	$db = "oci:dbname=spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr;charset=AL32UTF8";
	
	/*Localhost*/
	// $login = 'copie_tdf';
	// $mdp = 'copie_tdf';
	// $db = fabriquerChaineConnexion2();
	
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
	
	//affichage du tableau contenant les informations une fois que la date est renseigné :
	function affichage() {
		global $conn, $annee, $curLigne, $curNbVainqueur;
		
		//affichage de l'entête du tableau quoi qu'il ce passe :
		$style = "style=\"border: 1px solid black; margin: auto;\"";
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

			//boucle d'affichage des lignes :
			$j =0; //parcour du tableau de resultat
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
			//sinon on ferme le tableau et informe l'utilisateur qu'il faut selectionner une année
			echo "</table>";
			echo '<p style="text-align :center">Pas encore d\'année sélectionnée !</p>';
		}
	}
	
	//affiche une ligne du tableau :
	function afficheLigneTableau($tab, $style) {
		
		//si vainqueur unique
		if (!isset($tab[1])) {
			echo '<tr '.$style.'>
				<th '.$style.'>'.$tab[0]['N_EPREUVE'].'</th>
				<th '.$style.'>'.$tab[0]['DISTANCE'].'</th>
				<th '.$style.'>'.$tab[0]['JOUR'].'</th>
				<th '.$style.'>'.$tab[0]['NOM']. ' ' .$tab[0]['PRENOM'].'</th>
				<th '.$style.'>'.$tab[0]['HEURE']. 'h/' .$tab[0]['MINUTE']. 'min/' .$tab[0]['SECONDE'].'s</th>
				<th '.$style.'>'.$tab[0]['TOTAL_SECONDE'].'</th>
				</tr>';
		}
		else {
			//si victoire en équipe
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
	
	//affichage de tout les membres en cas de victoire par équipe :
	function afficheNomPrenom($tab) {
		$retour = '';
		foreach ($tab as $ligne) {
			$retour = $retour . $ligne['NOM']. ' ' . $ligne['PRENOM'].'<br>';
		}

		return $retour;
	}

	//LE FICHIER HTML:
	include("../html/affichageEtape.html");

?>