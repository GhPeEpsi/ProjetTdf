<?php
	include ("pdo_oracle.php");
	include ("util_affichage.php");
	include ("verificationsForm.php");
	
	/* --------------------------------------------------------------------------------------------------------------------------------- */
	/* ----------------------------------------------------Connexion-------------------------------------------------------------------- */
	/* --------------------------------------------------------------------------------------------------------------------------------- */

	$login = 'ETU2_49';
	$mdp = 'ETU2_49';
	$db = "oci:dbname=spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr;charset=AL32UTF8";
	// $db = fabriquerChaineConnexion();

	//Connexion locale Bastien
	// $login = 'projet_php';
	// $mdp = 'projet_php';
	// $db = fabriquerChaineConnexion2();

	//Connexion locale Jérémy
	//$login = 'copie_tdf_copie';
	//$mdp = 'copie_tdf_copie';
	// $db = "oci:dbname=localhost:1521/xe;charset=AL32UTF8";

	$conn = OuvrirConnexion($db,$login,$mdp);
	
	$req = 'SELECT * FROM tdf_coureur ORDER BY nom';
	$nbLignes = LireDonnees1($conn,$req,$tab);
	
	if (!isset($_GET['numCoureur']) || empty($_GET['numCoureur'])) {
		echo '<script> alert(\'Il n\'est pas permis de modifier l\'URL\'); </script>';
		return;
	}


	/* -------------------------------------------------------------------------------------------------------------------------------- */
	/* --------------------------------------------Récupération des données------------------------------------------------------------ */
	/* -------------------------------------------------------------------------------------------------------------------------------- */

	
	// récupération du numéro du coureur pour l'insérer dans son input correspondant
	function getNumeroCoureur($conn) {
		$req = 'SELECT n_coureur FROM tdf_coureur WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req,$tab);
		echo utf8_encode($tab[0]['N_COUREUR']);
	}
	// récupération du numéro du coureur pour l'insérer dans une variable de comparaison
	function getNumeroCoureur2($conn) {
		$req = 'SELECT n_coureur FROM tdf_coureur WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req,$tab);
		return utf8_encode($tab[0]['N_COUREUR']);
	}
	
	// définition d'une variable de comparaison comprenant le numéro du coureur sélectionné pour qu'il soit inchangeable si l'utilisateur tente de le modifier
	$nCoureurInchangeable = getNumeroCoureur2($conn);

	// récupération du nom du coureur pour l'insérer dans son input correspondant
	function getNomCoureur($conn) {
		$req = 'SELECT nom FROM tdf_coureur WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req,$tab);
		echo utf8_encode($tab[0]['NOM']);
	}
	// récupération du nom du coureur pour l'insérer dans une variable de comparaison
	function getNomCoureur2($conn) {
		$req = 'SELECT nom FROM tdf_coureur WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req,$tab);
		return utf8_encode($tab[0]['NOM']);
	}
	
	// récupération du prénom du coureur pour l'insérer dans son input correspondant
	function getPrenomCoureur($conn) {
		$req = 'SELECT prenom FROM tdf_coureur WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req,$tab);
		echo $tab[0]['PRENOM'];
	}
	// récupération du prénom du coureur pour l'insérer dans une variable de comparaison
	function getPrenomCoureur2($conn) {
		$req = 'SELECT prenom FROM tdf_coureur WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req,$tab);
		return $tab[0]['PRENOM'];
	}

	// récupération du pays du coureur pour l'insérer dans son input correspondant
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

	// récupération de l'année de naissance du coureur pour l'insérer dans son input correspondant
	function getAnneeNaissanceCoureur($conn) {
		$req = 'SELECT annee_naissance FROM tdf_coureur WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req,$tab);
		echo utf8_encode($tab[0]['ANNEE_NAISSANCE']);
		return utf8_encode($tab[0]['ANNEE_NAISSANCE']);
	}

	// récupération de l'année de la première participation du coureur pour l'insérer dans son input correspondant
	function getAnneePremiereCoureur($conn) {
		$req = 'SELECT annee_prem FROM tdf_coureur WHERE n_coureur = \''.$_GET['numCoureur'].'\'';
		$nbLignes1 = LireDonnees1($conn,$req,$tab);
		echo utf8_encode($tab[0]['ANNEE_PREM']);
		return utf8_encode($tab[0]['ANNEE_PREM']);
	}

	// définition de variables de comparaison
	$nCoureurInchangeable = getNumeroCoureur2($conn);
	$paysInchangeable = getPaysCoureur($conn);
	$prenomInchangeable = getPrenomCoureur2($conn);
	$nomInchangeable = getNomCoureur2($conn);

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
		global $conn, $regex, $paysInchangeable, $prenomInchangeable, $nomInchangeable;
		
		$req = 'select count(*) as nb from tdf_coureur 
				join tdf_app_nation using (n_coureur)
				where nom = \''.testNom($_POST['nomCoureur'], $regex).'\'
				and prenom = \''.testPrenom($_POST['prenomCoureur'], $regex).'\'';
				
		LireDonnees1($conn, $req, $tab);
		
		$req1 = 'select nom, prenom from tdf_coureur where n_coureur = '.$_GET['numCoureur'];
		LireDonnees1($conn, $req1, $tab1);
		
		// on crée une variable pour savoir si au moins le prénom ou le nom a été modifié
		if (($tab1[0]['NOM'] == $_POST['nomCoureur']) && ($tab1[0]['PRENOM'] == $_POST['prenomCoureur'])) {
			$nomPrenomOk = true;
		}
		else {
			$nomPrenomOk = false;
		}

		// on insère les données dans la base dans le cas où il n'existe pas un coureur avec les même prénom et nom
		if (($tab[0]['NB'] != 0) && !$nomPrenomOk) { // <-- si au moins le prénom ou le nom a été changé et qu'un coureur possède déjà ce duet
			echo '<script>alert(\'Un coureur ne peut pas posséder les mêmes nom et prénom qu\'un coureur qui existe déjà\');</script>';
		} else { // insertion des modifications dans la base
			setNomCoureur($conn, $regex);
			setPrenomCoureur($conn, $regex);
			setPaysCoureur($conn);
			setAnneeNaissanceCoureur($conn);
			setAnneePremiereCoureur($conn);
			echo '<script>alert(\'Modifications enregistrées\');</script>';
		}
	}

		
	/* -------------------------------------------------------------------------------------------------------------------------------- */
	/* -----------------------------------------Vérification du bon remplissage des champs--------------------------------------------- */
	/* -------------------------------------------------------------------------------------------------------------------------------- */

	// Vérifie : si les champs obligatoires sont remplis, si les champs sont correctement remplis (regex)
	// Si tout est vérifié : la page est soumise et les informations envoyées/modifiées
	if(isset($_POST['envoyer'])) { // si l'on clique sur le bouton envoyer
		if (empty($_POST['numCoureur']) || empty($_POST['nomCoureur']) || empty($_POST['prenomCoureur']) || ($_POST['nationCoureur'] == 'NATIONALITÉ')) { // Teste si les champs obligatoires sont vides / Même si on ne peut pas modifier numCoureur, si jamais il venait à être vide, il ne faut pas soumettre les informations.
			echo "<script> alert('Vous n\'avez pas rempli certains champs obligatoires'); </script>";
		} else if ($_POST['numCoureur'] != $nCoureurInchangeable) { // Si le numéro de coureur modifié par l'utilisateur n'est pas vide, teste s'il est différent du numéro actuel du coureur pour l'empêcher de le modifier
			echo "<script> alert('Il est STRICTEMENT INTERDIT de modifier le numéro du coureur'); </script>";
		} else { // Sinon, on fait les tests des valeurs entrées grâce aux regex
			if(!empty($_POST['anneeNaissanceCoureur']) && !empty($_POST['anneePremiereCoureur'])) { // On teste si annee_naissance et annee_prem ne sont pas vides
				if (!empty(testNom($_POST['nomCoureur'], $regex)) && !empty(testPrenom($_POST['prenomCoureur'], $regex)) && !empty(testDate($_POST['anneeNaissanceCoureur'])) && !empty(testDate($_POST['anneePremiereCoureur']))) { // On teste si les données saisies sont correctes
					if ($_POST['anneePremiereCoureur'] >= $_POST['anneeNaissanceCoureur']) { // Si annee_prem est supérieure à annee_naissance on insère les données dans la base
						toutInserer();
					} else { // Sinon on avertit l'utilisateur
						echo "<script> alert('La première année de participation doit être supérieure ou égale à l\'année de naissance'); </script>";
					}
				}
			} else if (empty($_POST['anneeNaissanceCoureur']) && empty($_POST['anneePremiereCoureur'])) { // On teste si annee_naissance et annee_prem sont vides
				if (!empty(testNom($_POST['nomCoureur'], $regex)) && !empty(testPrenom($_POST['prenomCoureur'], $regex))) { // Si les données saisies sont correctes on insère dans la base
					toutInserer();
				}
			} else if (!empty($_POST['anneeNaissanceCoureur']) && empty($_POST['anneePremiereCoureur'])) { // Si annee_naissance est remplie et annee_prem est vide
				if (!empty(testNom($_POST['nomCoureur'], $regex)) && !empty(testPrenom($_POST['prenomCoureur'], $regex)) && !empty(testDate($_POST['anneeNaissanceCoureur']))) { // Si les données saisies sont correctes on insère dans la base
					toutInserer();
				}
			} else if (empty($_POST['anneeNaissanceCoureur']) && !empty($_POST['anneePremiereCoureur'])) { // Si annee_naissance est vide et annee_prem est remplie
				if (!empty(testNom($_POST['nomCoureur'], $regex)) && !empty(testPrenom($_POST['prenomCoureur'], $regex)) && !empty(testDate($_POST['anneePremiereCoureur']))) { // Si les données saisies sont correctes on insère dans la base
					toutInserer();
				}
			}
		}
	}

	include ("../html/navBar.html");
	include ("../html/modificationCoureur.html");
?>