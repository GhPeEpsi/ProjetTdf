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
		$req = 'SELECT n_coureur FROM tdf_coureur where n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req,$tab);
		echo utf8_encode($tab[0]['N_COUREUR']);
	}
	
	function getNomCoureur($conn) {
		$req = 'SELECT nom FROM tdf_coureur where n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req,$tab);
		echo utf8_encode($tab[0]['NOM']);
	}
	
	function getPrenomCoureur($conn) {
		$req = 'SELECT prenom FROM tdf_coureur where n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req,$tab);
		echo utf8_encode($tab[0]['PRENOM']);
	}

	function getPaysCoureur($conn) {
		$req = 'SELECT tdf_nation.nom FROM tdf_nation
				JOIN tdf_app_nation USING (code_cio)
				WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req,$tab);
		return utf8_encode($tab[0]['NOM']);
	}

	function getListePays($conn) {
		$req = 'SELECT code_cio, nom FROM tdf_nation where annee_disparition is null order by nom';
		$nbLignes = LireDonnees1($conn,$req,$tab);
		$paysCoureur = getPaysCoureur($conn);
		for ($i=0; $i<$nbLignes; $i++) {
			if($tab[$i]['NOM'] == $paysCoureur) {
				$tab[$i]["NOM"] = utf8_encode($tab[$i]["NOM"]);
				echo '<option value="'.$tab[$i]['CODE_CIO'].'" selected>'.$tab[$i]['NOM'];
				echo '</option>';
			} else {
				$tab[$i]["NOM"] = utf8_encode($tab[$i]["NOM"]);
				echo '<option value="'.$tab[$i]['CODE_CIO'].'">'.$tab[$i]['NOM'];
				echo '</option>';
			}
		}
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