<?php
	if (isset($_POST['infos'])) {
		header("location:affichageCoureur.php?numCoureur=".$_POST['nomCoureur']);
		exit;
	}
	else if (isset($_POST['modifier'])) {
		header("location:modificationCoureur.php?numCoureur=".$_POST['nomCoureur']);
		exit;
	}

	include ("pdo_oracle.php");
	include ("util_affichage.php");
	include ("../html/navBar.html");

	// $login = 'ETU2_49';
	// $mdp = 'ETU2_49';
	// $db = fabriquerChaineConnexion();	

	$login = 'projet_php';
	$mdp = 'projet_php';
	$db = fabriquerChaineConnexion2();

	$conn = OuvrirConnexion($db,$login,$mdp);
	$req = 'SELECT * FROM tdf_coureur order by nom';
	$nbLignes = LireDonnees1($conn,$req,$tab);

	if (!empty($_POST)) {
		if (isset($_POST['coureur'])) {
			$cour = $_POST['coureur'];
			echo ("Coureur $cour sélectionné");
		}
	}
	else {
		include("../html/choixCoureur.html");
	}

	function listeCoureurs($tab,$nbLignes) {
		for ($i=0; $i<$nbLignes; $i++) {
			$tab[$i]["PRENOM"] = utf8_encode($tab[$i]["PRENOM"]);
			echo '<option value="'.$tab[$i]["N_COUREUR"].'">'.$tab[$i]['NOM'].' '.$tab[$i]['PRENOM'];
			echo '</option>';
		}
	}
?>