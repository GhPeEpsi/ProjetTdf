<?php
	//positionné la pour eviter les echo de la navbar
	if (isset($_POST['annuler'])) {
		header('Location: choixCoureur.php');
	}


	include ("pdo_oracle.php");
	include ("util_affichage.php");
	include ("../html/navBar.html");

	//ce fichier sert de page de vérification a la suppression 
	//d'un coureur via choixCoureur (menu gerer coureur) :
	
	//Serveur UNICAEN
	$login = 'ETU2_49';
	$mdp = 'ETU2_49';
	$db = "oci:dbname=spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr;charset=AL32UTF8";
	
	//Localhost
	// $login = 'projet_php';
	// $mdp = 'projet_php';
	// $db = "oci:dbname=localhost:1521/xe;charset=AL32UTF8";

	
	$conn = OuvrirConnexion($db,$login,$mdp);

	//récupération du numero de coureur ou message à l'utilisateur :
	if (!empty($_GET['numCoureur'])) {
		$n_coureur = intval($_GET['numCoureur']);
	}
	else {
		echo 'on ne touche pas à l\'url svp !';
		return;
	}

	//lancement de la fonction principale si l'utilisateur à validé :
	if (isset($_POST['valider'])) {
		supprimer($n_coureur);
	}
	
	// affiche le nom, prenom d'un coureur à supprimer :
	function afficherCoureur() {
		global $conn, $n_coureur;
		$req = 'select nom, prenom from tdf_coureur where n_coureur = '.$n_coureur;
		LireDonnees1($conn, $req, $tab);
		if ($tab != null)
			echo '<p>Voulez-vous supprimer ce coureur : '.$tab[0]['NOM']. ' ' .$tab[0]['PRENOM'].' ?</p>';
	}

	//Suppression du coureur :
	function supprimer($n_coureur){
		global $conn;
		
		//vérification que le coureur n'a pas de participation au tdf :
		$req = 'select count(*) from tdf_coureur
				join TDF_PARTI_COUREUR using (n_coureur)
				where n_coureur = '.$n_coureur;
		LireDonnees1($conn, $req, $tab);
		
		if ($tab[0]['COUNT(*)'] != 0) {
			echo 'on ne touche pas à l\'url svp !';
			return;
		}
		
		//suppression :
		$reqAppNation = "delete from tdf_app_nation where n_coureur=".$n_coureur;
		$reqCoureur = "delete from tdf_coureur where n_coureur=".$n_coureur;
		majDonnees($conn,$reqAppNation);
		majDonnees($conn,$reqCoureur);
		header('Location: choixCoureur.php');
		echo "<p>Coureur bien enlevé de la base !</p>";
	}

	//Le fichier html:
	include("../html/supprimerUnCoureur.html");
?>