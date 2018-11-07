<?php
	if(empty($_GET)) {
		include ("../html/navBar.html");
	}
	include ("pdo_oracle.php");
	include ("util_affichage.php");
	include ("verificationsForm.php");

	$texteFinal = "";
	// connexion à la base
	 $db_username = 'ETU2_49';
	 $db_password = 'ETU2_49';
	 $db = "oci:dbname=spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr;charset=AL32UTF8";


	//connection de Jérémy qui resté là après la merge
	// $db_username = 'copie_tdf_copie';
	// $db_password = 'copie_tdf_copie';
	// $db = "oci:dbname=localhost:1521/xe;charset=AL32UTF8";
	//$db = fabriquerChaineConnexion();


	// $db_username = 'copie_tdf';
	// $db_password = 'copie_tdf';
	// $db = "oci:dbname=localhost:1521/xe;charset=AL32UTF8";
	$conn = OuvrirConnexion($db,$db_username,$db_password);

	//traitement :
	
	if (isset($_GET['nom'])) {
		$nom = testNomSponsor($_GET['nom']);
		if($nom != NULL) {
			echo substr($nom, 0, 3);
		}
	}

	// if(!empty($_POST['nom'])){
	// 	$nom = $_POST['nom'];
	// 	$nom = testNomSponsor($nom, $regex);
	// 	$temporaire = $_POST['nomAbrege'];
	// 	$nomAbrege = substr($nom, 0, 3);
	// 	echo $nomAbrege;
	// }

	$nat = ajoutSelection();
	$req = 'SELECT code_cio, nom FROM tdf_nation where annee_disparition is null order by nom';  //A voir pour mettre dans la fonction remplir option au debut
	$nbLignes = LireDonnees1($conn,$req,$tab);

	// condition pour que rien ne se passe si tout n'est pas rempli, sinon, ajout du coureur à la base grace à la requête
	if(isset($_POST['verifier'])){

		//verfication du bon remplissage des champs obligatoire :
		if (empty($_POST['nom']) || empty($_POST['nomAbrege']) || $_POST['nationalite'] == ''){
			$textFinal = $texteFinal."<br> Vous n'avez pastout rempli";
		}else{
			if(!empty($_POST['nom'])){
				$nom = $_POST['nom'];
				$nom = testNomSponsor($nom, $regex);
			}
			if(!empty($_POST['nomAbrege'])){
				$nomAbrege = $_POST['nomAbrege'];
				$nomAbrege = testNomAbrege($nomAbrege, $regex);
			}

			if(!empty($_POST['dateC'])){
				$verifInt = $_POST['dateC'];
				if(!ctype_digit($verifInt)|| $verifInt != date('Y')){
						$textFinal = $texteFinal."<br> Vous n'avez pas entré une année valide";
					}else{
						$dateC = recupAnnee();
					}
			}
		}
	}

	
	

	//FUNCTION :
	
	function afficherTexteFinal(){
		global $textFinal;
		echo $textFinal;
	}

	//verifier que le coureur qu'on veut entrer n'existe pas deja :
	// function nonExistant() {
	// 	global $conn, $nom, $prenom, $nat;

	// 	$req = 'select count(*) as nb from tdf_coureur 
	// 	join tdf_app_nation using (n_coureur)
	// 	where nom = \''.$nom.'\'
	// 	and prenom = \''.$prenom.'\'
	// 	and code_cio = \''.$nat.'\'';

	// 	LireDonnees1($conn, $req, $tab);

	// 	if ($tab[0]['NB'] == 0) {
	// 		return true;
	// 	}
	// 	return false;
	// }

	//permet d'aller voir les infos d'un coureur qui vient d'être entré :
	// if(isset($_POST['regarder'])){
	// 	if (isset($_POST['droitPassage']) && $_POST['droitPassage']=="true") {
	// 		$sql3 = "SELECT max(n_coureur) as max from tdf_coureur";
	// 		LireDonnees1($conn,$sql3,$tab3);
	// 		header ("location:affichageCoureur.php?numCoureur=".$tab3[0]['MAX']);
	// 	}
	// }

	// function droitPassage() {
	// 	if (isset($_POST['verifier']) && isset($_POST['droitPassage']) && ($_POST['droitPassage']=="false")) {
	// 		echo "true";
	// 		return;
	// 	}
	// 	else if (isset($_POST['droitPassage']) && $_POST['droitPassage']=="true") {
	// 		echo "true";
	// 		return;
	// 	}
	// 	echo "false";
	// }
	
	function remplirDernierSponsor() {
		global $conn;
		$req = 'select n_equipe, n_sponsor, nom, na_sponsor, code_cio,annee_sponsor 
				from tdf_sponsor where (n_equipe, n_sponsor) in
				(
					select n_equipe, max(n_sponsor)
					from tdf_sponsor
					group by n_equipe
				)
				order by n_equipe';
		
		$nb = LireDonnees1($conn, $req, $tab);
		
		echo '<option value="null">Choisir un sponsor a mettre à jour</option>';
		
		foreach ($tab as $sponsor) {
			echo '<option value="'. $sponsor['N_EQUIPE'] .'">'. $sponsor['NOM'] .'</option>';
		}
			
	}

	function recupAnnee(){
		if(!empty($_POST['dateC'])){
			$dateC = $_POST['dateC'];
			return intval($dateC);
		}
		return null;
	}


	// On remplis la liste deroulante avec les nationalité de la base
	function remplirOption($tab,$nbLignes) {
		
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

	function afficherDateC(){
	global $dateCreation;
	echo $dateCreation;
	}

	function afficherNomAbrege(){
		global $nomAbrege;
		echo $nomAbrege;
	}
	if (empty($GET)) {
		include ("../html/ajoutSponsor.html");
	}

?>