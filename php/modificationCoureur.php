<?php
	include ("pdo_oracle.php");
	include ("util_affichage.php");
	include ("verificationsForm.php");
	include ("../html/navBar.html");

	/*$login = 'ETU2_49';
	$mdp = 'ETU2_49';
	$db = fabriquerChaineConnexion();*/	

	$login = 'copie_tdf';
	$mdp = 'copie_tdf';
	$db = fabriquerChaineConnexion2();

	$conn = OuvrirConnexion($db,$login,$mdp);
	$req = 'SELECT * FROM tdf_coureur ORDER BY nom';
	$nbLignes = LireDonnees1($conn,$req,$tab);

	include ("../html/modificationCoureur.html");

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
	}

	// récupération de l'année de la première participation du coureur
	function getAnneePremiereCoureur($conn) {
		$req = 'SELECT annee_prem FROM tdf_coureur WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req,$tab);
		echo utf8_encode($tab[0]['ANNEE_PREM']);
	}

	// changement du nom du coureur
	function setNomCoureur($conn) {

	}

	// changement du prénom du coureur
	function setPrenomCoureur($conn) {

	}

	// insertion/changement du pays du coureur
	function setPaysCoureur($conn) {
		
	}

	// insertion/changement de l'année de naissance du coureur
	function setAnneeNaissanceCoureur($conn) {
		
	}

	// insertion/changement de l'année de a première participation du coureur
	function setAnneePremiereCoureur($conn) {
		
	}

	// Vérifie : si les champs obligatoires sont remplis, si les champs sont correctement remplis (regex)
	// Si tout est vérifié : la page est soumise et les informations envoyées/modifiées
	if(isset($_POST['envoyer'])) {
		// Même si on ne peut pas modifier numCoureur, si jamais il venait à être vide, il ne faut pas soumettre les informations.
		if (empty($_POST['numCoureur']) || empty($_POST['nomCoureur']) || empty($_POST['prenomCoureur']) || $_POST['nationCoureur'] == 'NATIONALITÉ') {
			echo "<script> alert('vous n\'avez pas tout rempli') </script>";
		} else {
			include ("../html/fichierInutileValidation.html");
		}
	}
	/*
	-- Création d'un coureur random
	INSERT INTO tdf_coureur(n_coureur, nom, prenom, annee_naissance)
	VALUES ((select max(n_coureur) from tdf_coureur) + 1, 'ZIGOUIGOUI', 'Bérangère', '1999');


	-- Changement du nom du coureur random
	update tdf_coureur
	set nom = 'BOUKIKOU'
	where n_coureur = 1797;


	-- Changement du prénom du coureur random
	update tdf_coureur
	set prenom = 'Takikabibi'
	where n_coureur = 1797;

	-- Ajout de la date de naissance du coureur random -- UNIQUEMENT SI ELLE EST VIDE
	INSERT INTO tdf_coureur(n_coureur, annee_naissance)
	VALUES (1797, 700);
	-- Changement de la date de naissance du coureur random -- UNIQUEMENT SI ELLE N'EST PAS VIDE
	update tdf_coureur
	set annee_naissance = 700
	where n_coureur = 1797;


	-- Ajout de la date_prem du coureur random -- UNIQUEMENT SI ELLE EST VIDE
	INSERT INTO tdf_coureur(n_coureur, annee_prem)
	VALUES (1797, 721);
	-- Changement de la date_prem du coureur random -- UNIQUEMENT SI ELLE N'EST PAS VIDE
	update tdf_coureur
	set annee_prem = 721
	where n_coureur = 1797;


	-- Ajout de la nationalité du coureur random -- UNIQUEMENT SI ELLE EST VIDE
	INSERT INTO tdf_app_nation(n_coureur, code_cio)
	VALUES (1797, 'FRA');
	-- Changement de la nationalité du coureur random -- UNIQUEMENT SI ELLE N'EST PAS VIDE
	update tdf_app_nation
	set code_cio = 'BEL'
	where n_coureur = 1797;
	*/
?>