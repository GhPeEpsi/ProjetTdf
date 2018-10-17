<?php
	include ("pdo_oracle.php");
	include ("util_affichage.php");

	$login = 'ETU2_49';
	$mdp = 'ETU2_49';
	$db = fabriquerChaineConnexion();	

/*	$login = 'copie_tdf_copie';
	$mdp = 'copie_tdf_copie';
	$db = fabriquerChaineConnexion2();	*/

	$conn = OuvrirConnexion($db,$login,$mdp);
	$req = 'SELECT * FROM tdf_coureur order by nom';
	$nbLignes = LireDonnees1($conn,$req,$tab);

	include ("../html/modificationCoureur.html");

	function getNumeroCoureur($conn) {
		$req1 = 'SELECT n_coureur FROM tdf_coureur where n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req1,$tab);
		echo utf8_encode($tab[0]['N_COUREUR']);
	}
	
	function getNomCoureur($conn) {
		$req1 = 'SELECT nom FROM tdf_coureur where n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req1,$tab);
		echo utf8_encode($tab[0]['NOM']);
	}
	
	function getPrenomCoureur($conn) {
		$req1 = 'SELECT prenom FROM tdf_coureur where n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req1,$tab);
		echo utf8_encode($tab[0]['PRENOM']);
	}
	
	function listeCoureurs($tab,$nbLignes) {
		for ($i=0; $i<$nbLignes; $i++) {
			$tab[$i]["PRENOM"] = utf8_encode($tab[$i]["PRENOM"]);
			echo '<option value="'.$tab[$i]["N_COUREUR"].'">'.$tab[$i]['NOM'].' '.$tab[$i]['PRENOM'];
			echo '</option>';
		}
	}

	function verificationEnvoi() {	
	}
?>