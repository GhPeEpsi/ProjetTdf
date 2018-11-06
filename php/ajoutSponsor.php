<?php
	include ("../html/navBar.html");
	include ("pdo_oracle.php");
	include ("util_affichage.php");
	include ("verificationsForm.php");

	$texteFinal = "";
	// connexion à la base
	 //$db_username = 'ETU2_49';
	 // $db_password = 'ETU2_49';
	 // $db = "oci:dbname=spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr;charset=AL32UTF8";


	//connection de Jérémy qui resté là après la merge
	// $db_username = 'copie_tdf_copie';
	// $db_password = 'copie_tdf_copie';
	// $db = "oci:dbname=localhost:1521/xe;charset=AL32UTF8";
	//$db = fabriquerChaineConnexion();


	$db_username = 'projet_php';
	$db_password = 'projet_php';
	$db = fabriquerChaineConnexion2();
	$conn = OuvrirConnexion($db,$db_username,$db_password);

	//traitement :
	
	
	
	

	//FUNCTION :
	
	function afficherTexteFinal(){
		global $textFinal;
		echo $textFinal;
	}

	//verifier que le coureur qu'on veut entrer n'existe pas deja :
	function nonExistant() {
		global $conn, $nom, $prenom, $nat;

		$req = 'select count(*) as nb from tdf_coureur 
		join tdf_app_nation using (n_coureur)
		where nom = \''.$nom.'\'
		and prenom = \''.$prenom.'\'
		and code_cio = \''.$nat.'\'';

		LireDonnees1($conn, $req, $tab);

		if ($tab[0]['NB'] == 0) {
			return true;
		}
		return false;
	}

	//permet d'aller voir les infos d'un coureur qui vient d'être entré :
	if(isset($_POST['regarder'])){
		if (isset($_POST['droitPassage']) && $_POST['droitPassage']=="true") {
			$sql3 = "SELECT max(n_coureur) as max from tdf_coureur";
			LireDonnees1($conn,$sql3,$tab3);
			header ("location:affichageCoureur.php?numCoureur=".$tab3[0]['MAX']);
		}
	}

	function droitPassage() {
		if (isset($_POST['verifier']) && isset($_POST['droitPassage']) && ($_POST['droitPassage']=="false")) {
			echo "true";
			return;
		}
		else if (isset($_POST['droitPassage']) && $_POST['droitPassage']=="true") {
			echo "true";
			return;
		}
		echo "false";
	}

	function recupAnnee(){
		if(!empty($_POST['dateN'])){
			$annee_naissance = $_POST['dateN'];
			return intval($annee_naissance);
		}
		return null;
	}


	// On remplis la liste deroulante avec les nationalité de la base
	function remplirOption($tab,$nbLignes) {
		global $nat;
		for ($i=0; $i<$nbLignes; $i++) {
			if ($nat == $tab[$i]['CODE_CIO']) {
				$tab[$i]["NOM"] = utf8_encode($tab[$i]["NOM"]);
				echo '<option value="'.$tab[$i]['CODE_CIO'].'" selected>'.$tab[$i]['NOM'];
				echo '</option>';
			}
			else{
				$tab[$i]["NOM"] = utf8_encode($tab[$i]["NOM"]);
				echo '<option value="'.$tab[$i]['CODE_CIO'].'">'.$tab[$i]['NOM'];
				echo '</option>';
			}
		}
	}

	//retourne la nationalité sélectionnée
	function ajoutSelection(){
		if (!empty($_POST)) {
			if (isset($_POST['nationalite'])) {
				$nat = $_POST['nationalite'];
				if($nat != "Nationalité"){
					return $nat;
				}
			}
		}
	}

	function afficherNom(){
	global $nom;
	echo $nom;
	}

	function afficherPrenom(){
	global $prenom;
	echo $prenom;
	}

	function afficherDateN(){
	global $dateNaissance;
	echo $dateNaissance;
	}

	function afficherDepuisQ(){
		global $depuisQuand;
		echo $depuisQuand;
	}

	//insertion des 
	if(empty($_GET)){
		
		include ("../html/ajoutCoureur.html");
	}
?>