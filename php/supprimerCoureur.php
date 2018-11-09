<?php
	include ("pdo_oracle.php");
	include ("util_affichage.php");
	include ("../html/navBar.html");


	//Serveur UNICAEN
	$login = 'ETU2_49';
	$mdp = 'ETU2_49';
	$db = "oci:dbname=spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr;charset=AL32UTF8";
	
	//Localhost
	// $login = 'projet_php';
	// $mdp = 'projet_php';
	// $db = fabriquerChaineConnexion2();
	
	$conn = OuvrirConnexion($db,$login,$mdp);

	//Affichage de la liste des coureurs qui peuvent êtres supprimer :
	$tabParticipation;
	function afficheListe() {
		global $conn, $tabParticipation;
		
		// recherche des coureurs n'ayant jamais participé au tdf :
		$reqBase = 
			"select n_coureur, nom, prenom from tdf_coureur
			where n_coureur not in 
			(
				select n_coureur from tdf_parti_coureur
			)
			order by n_coureur";
		$nbcoureurs = LireDonnees1($conn,$reqBase,$tabParticipation);
		
		if ($nbcoureurs == 0) //s'il n'y a pas de coureur supprimable affichage d'un message :
			echo "<h3>Vous ne pouvez pas supprimer de coureur a partir du moment où ils ont tous participé à au moins 1 tdf</h3>";
		else //sinon affichage de la liste :
			afficherCheck($tabParticipation);
	}
	
	//affichage de la liste des coureurs supprimables avec leurs checkbox :
	function afficherCheck($tab) {
		$i =0;
		echo '<fieldset>';
		
		//affichage de la checkbox pour cocher tt les coureurs :
		echo '<input type="checkbox" name="tout" value="tout"  onclick="toutCocher2()"> Tout cocher/décocher<br />';
		
		//affichage des lignes
		foreach ($tab as $coureur) {
			echo '<input type="checkbox" id="id'.$i.'" name="aSupprimer[]" value="'.$coureur['N_COUREUR'].'">
				  <label for="id'.$i.'">'.$coureur['NOM']. ' ' . $coureur['PRENOM'].'</label><br>';
			$i++;
		}
		echo '</fieldset>';
		
		//Affichage des boutons de submit :
		echo "<input type=\"submit\" name=\"supp\" value=\"Supprimer\">";
	}

	//traitement des informations après submit :
	if (isset($_POST['supp'])) {
		if (isset($_POST['aSupprimer'])) {
			$tabCoureur = $_POST['aSupprimer'];
			supprimer($tabCoureur);
		}
		else {
			echo "<p>Veuillez choisir un coureur valide !</p>";
		}
	}

	//supprimer tout les coureur :
	function supprimer($tab){
		global $conn;
		foreach($tab as $n) {
			if (okInsertion($n)) { //Verification du nombre de participation de coureur pour eviter les modification dans l'éditeur :
				$reqAppNation = "delete from tdf_app_nation where n_coureur=".$n;
				$reqCoureur = "delete from tdf_coureur where n_coureur=".$n;
				majDonnees($conn,$reqAppNation); //supprimer son appartenance a une nation
				majDonnees($conn,$reqCoureur); //supprimer le coureur
				echo "<p>Coureur bien enlevé de la base !</p>";
			}
		}
		
	}
	
	//vérification que le coureur n'a pas de participation au tdf pour eviter de supprimer un coureur valide :
	function okInsertion($n_coureur) {
		global $conn;
		
		$req = 'select count(*) from tdf_coureur
				join TDF_PARTI_COUREUR using (n_coureur)
				where n_coureur = '.$n_coureur;
		LireDonnees1($conn, $req, $tab);
		
		if ($tab[0]['COUNT(*)'] != 0) {
			echo 'on ne touche pas au code source svp !';
			return false;
		}
		return true;
	}

	//Le fichier html:
	include("../html/supprimerCoureur.html");
?>