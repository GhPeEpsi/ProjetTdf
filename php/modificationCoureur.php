<?php
	include ("pdo_oracle.php");
	include ("util_affichage.php");
	include ("verificationsForm.php");
	

	$login = 'ETU2_49';
	$mdp = 'ETU2_49';
	$db = fabriquerChaineConnexion();

	// $login = 'copie_tdf';
	// $mdp = 'copie_tdf';
	// $db = fabriquerChaineConnexion2();

	//$login = 'copie_tdf_copie';
	//$mdp = 'copie_tdf_copie';
	//$db = fabriquerChaineConnexion2();

	$conn = OuvrirConnexion($db,$login,$mdp);
	
	$req = 'SELECT * FROM tdf_coureur ORDER BY nom';
	$nbLignes = LireDonnees1($conn,$req,$tab);


	/* -------------------------------------------------------------------------------------------------------------------------------- */
	/* --------------------------------------------Récupération des données------------------------------------------------------------ */
	/* -------------------------------------------------------------------------------------------------------------------------------- */

	
	// récupération du numéro du coureur
	function getNumeroCoureur($conn) {
		$req = 'SELECT n_coureur FROM tdf_coureur WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req,$tab);
		echo utf8_encode($tab[0]['N_COUREUR']);
	}
	
	// récupération du nom du coureur
	function getNomCoureur($conn) {
		$req = 'SELECT nom FROM tdf_coureur WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req,$tab);
		echo utf8_encode($tab[0]['NOM']);
	}
	
	// récupération du prénom du coureur
	function getPrenomCoureur($conn) {
		$req = 'SELECT prenom FROM tdf_coureur WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req,$tab);
		echo utf8_encode($tab[0]['PRENOM']);
	}

	// récupération du pays du coureur
	function getPaysCoureur($conn) {
		$req = 'SELECT tdf_nation.nom FROM tdf_nation JOIN tdf_app_nation USING (code_cio) WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req,$tab);
		return utf8_encode($tab[0]['NOM']);
	}

	// récupération de la liste des nationalités/pays existant dans la base
	function getListePays($conn) {
		$req = 'SELECT code_cio, nom FROM tdf_nation WHERE annee_disparition IS NULL ORDER BY nom';
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

	// récupération de l'année de naissance du coureur
	function getAnneeNaissanceCoureur($conn) {
		$req = 'SELECT annee_naissance FROM tdf_coureur WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req,$tab);
		echo utf8_encode($tab[0]['ANNEE_NAISSANCE']);
		return utf8_encode($tab[0]['ANNEE_NAISSANCE']);
	}

	// récupération de l'année de la première participation du coureur
	function getAnneePremiereCoureur($conn) {
		$req = 'SELECT annee_prem FROM tdf_coureur WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req,$tab);
		echo utf8_encode($tab[0]['ANNEE_PREM']);
		return utf8_encode($tab[0]['ANNEE_PREM']);
	}


	/* -------------------------------------------------------------------------------------------------------------------------------- */
	/* -------------------------------------------------Écriture des données----------------------------------------------------------- */
	/* -------------------------------------------------------------------------------------------------------------------------------- */
	

	// changement du nom du coureur
	function setNomCoureur($conn, $regex) {
		$req = 'UPDATE tdf_coureur SET nom = \''.testNom($_POST['nomCoureur'], $regex).'\' WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		majDonnees($conn,$req);
	}

	// changement du prénom du coureur
	function setPrenomCoureur($conn, $regex) {
		$req = 'UPDATE tdf_coureur SET prenom = \''.testPrenom($_POST['prenomCoureur'], $regex).'\' WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		majDonnees($conn,$req);
	}

	// insertion/changement du pays du coureur
	function setPaysCoureur($conn) {
		$req = 'UPDATE tdf_app_nation SET code_cio = \''.$_POST['nationCoureur'].'\' WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		majDonnees($conn,$req);		
	}

	// insertion/changement de l'année de naissance du coureur
	function setAnneeNaissanceCoureur($conn) {
		if(empty($_POST['anneeNaissanceCoureur'])) {
			$req = 'UPDATE tdf_coureur SET annee_naissance = \'\' WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		} else {
			$req = 'UPDATE tdf_coureur SET annee_naissance = '.testDate($_POST['anneeNaissanceCoureur']).' WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		}
		majDonnees($conn,$req);
	}

	// insertion/changement de l'année de a première participation du coureur
	function setAnneePremiereCoureur($conn) {
		if(empty($_POST['anneePremiereCoureur'])) {
			$req = 'UPDATE tdf_coureur SET annee_prem = \'\' WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		} else {
			$req = 'UPDATE tdf_coureur SET annee_prem = '.$_POST['anneePremiereCoureur'].' WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		}
		majDonnees($conn,$req);
	}
	
	//fonction qui permet de lancer toutes les fonctions d'insertion :
	function toutInserer() {
		global $conn, $regex;
		
		$req = 'select count(*) as nb from tdf_coureur 
				join tdf_app_nation using (n_coureur)
				where nom = \''.testNom($_POST['nomCoureur'], $regex).'\'
				and prenom = \''.testPrenom($_POST['prenomCoureur'], $regex).'\'
				and code_cio = \''.$_POST['nationCoureur'].'\'';
				
		LireDonnees1($conn, $req, $tab);
		
		if ($tab[0]['NB'] == 0) {
			setNomCoureur($conn, $regex);
			setPrenomCoureur($conn, $regex);
			setPaysCoureur($conn);
			setAnneeNaissanceCoureur($conn);
			setAnneePremiereCoureur($conn);
		} else {
			echo '<script>alert(\'Un coureur ne peut pas posséder les même nom, prenom, et nationalité d\'un coureur qui existe déjà\');</script>';
		}
	}


	/* -------------------------------------------------------------------------------------------------------------------------------- */
	/* -----------------------------------------Vérification du bon remplissage des champs--------------------------------------------- */
	/* -------------------------------------------------------------------------------------------------------------------------------- */

	// Vérifie : si les champs obligatoires sont remplis, si les champs sont correctement remplis (regex)
	// Si tout est vérifié : la page est soumise et les informations envoyées/modifiées
	print_r($_POST);
	if(isset($_POST['envoyer'])) {
		// Même si on ne peut pas modifier numCoureur, si jamais il venait à être vide, il ne faut pas soumettre les informations.
		if (empty($_POST['numCoureur']) || empty($_POST['nomCoureur']) || empty($_POST['prenomCoureur']) || ($_POST['nationCoureur'] == 'NATIONALITÉ')) {
			echo "<script> alert('Vous n\'avez pas rempli certains champs obligatoires') </script>";
		} else {
			if(!empty($_POST['anneeNaissanceCoureur']) && !empty($_POST['anneePremiereCoureur'])) {
				if (!empty(testNom($_POST['nomCoureur'], $regex)) && !empty(testPrenom($_POST['prenomCoureur'], $regex)) && !empty(testDate($_POST['anneeNaissanceCoureur'])) && !empty(testDate($_POST['anneePremiereCoureur']))) {
					if ($_POST['anneePremiereCoureur'] >= $_POST['anneeNaissanceCoureur']) {
						toutInserer();
					} else {
						echo "<script> alert('La première année de participation doit être supérieure ou égale à l'année de naissance') </script>";
					}
				}
			} else if (empty($_POST['anneeNaissanceCoureur']) && !empty($_POST['anneePremiereCoureur'])) {
				if (!empty(testDate($_POST['anneePremiereCoureur']))) {
					echo "<script> alert('Le coureur ne peut pas posséder une première année de participation s'il ne possède pas d'année de naissance') </script>";
				}
			} else if (empty($_POST['anneeNaissanceCoureur']) && empty($_POST['anneePremiereCoureur'])) {
				if (!empty(testNom($_POST['nomCoureur'], $regex)) && !empty(testPrenom($_POST['prenomCoureur'], $regex))) {
					toutInserer();
				}
			} else if (!empty($_POST['anneeNaissanceCoureur']) && empty($_POST['anneePremiereCoureur'])) {
				if (!empty(testNom($_POST['nomCoureur'], $regex)) && !empty(testPrenom($_POST['prenomCoureur'], $regex)) && !empty(testDate($_POST['anneeNaissanceCoureur']))) {
					toutInserer();
				}
			}
		}
	}

	include ("../html/navBar.html");
	include ("../html/modificationCoureur.html");
?>