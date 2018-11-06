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

	 $db_username = 'ETU2_49';
	 $db_password = 'ETU2_49';
	 $db = "oci:dbname=spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr;charset=AL32UTF8";
	 $conn = OuvrirConnexion($db,$db_username,$db_password);

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
			echo '<option value="'.$tab[$i]["N_COUREUR"].'">'.$tab[$i]['NOM'].' '.$tab[$i]['PRENOM'];
			echo '</option>';
		}
	}
?>