<?php
	if(empty($_GET)) {
		include ("../html/navBar.html");
	}
	include ("pdo_oracle.php");
	include ("util_affichage.php");
	include ("verificationsForm.php");

	$textFinal = "";
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
	
	$reqAjout = 'Insert into tdf_sponsor(n_equipe, n_sponsor, nom, na_sponsor, code_cio, annee_sponsor)
				values(:n_equipe, (select max(n_sponsor) from tdf_sponsor)+1,:nomSpon, :nas, :cio, :annee)';
	
	$curAjout = preparerRequete($conn,$reqAjout);
	
	

	// condition pour que rien ne se passe si tout n'est pas rempli, sinon, ajout du coureur à la base grace à la requête
	if(isset($_POST['verifier'])){

		//verfication du bon remplissage des champs obligatoire :
		//Si on oublie les un des champs et ca passe pas :
		if (empty($_POST['nom']) || empty($_POST['nomAbrege']) || $_POST['nationalite'] == ''){
			$textFinal = $textFinal."<br> Vous n'avez pastout rempli";
		}else{ //sinon on récupére les infos
			if(!empty($_POST['nom'])){ //Test et modifications du nom
				$nom = $_POST['nom'];
				$nom = testNomSponsor($nom, $regex);
			}
			if(!empty($_POST['nomAbrege'])){ //Test et modifications du nom abregé
				$nomAbrege = $_POST['nomAbrege'];
				$nomAbrege = testNomAbrege($nomAbrege, $regex);
			}

			if(!empty($_POST['dateC'])){ //Test et modifications de la date
				$verifInt = $_POST['dateC'];
				if(!ctype_digit($verifInt)|| $verifInt != date('Y')){
					$textFinal = $textFinal."<br> Vous n'avez pas entré une année valide";
				}else{
					$dateC = recupAnnee();
				}
			}
			
			if ($textFinal == "") {
				enregistrementDonnées();
			}
		}
	}

	//print_r($_POST);
	//récupération du numéro de sponsor :
	$numSpon = 'null';
	if (isset($_POST['sponsor'])) {
		$numSpon = $_POST['sponsor'];
	}
	
	
	

	//FUNCTION :
	function enregistrementDonnées() {
		global $curAjout, $dateC, $nomAbrege, $nom, $numSpon;
		
		if ($numSpon == "null") {
			echo "c'est la merde";
		}
		/*
		ajouterParam($curAjout,':date',$dateC);
		ajouterParam($curAjout,':nas',$nomAbrege);
		ajouterParam($curAjout,':nom',$nom);
		
		majDonneesPreparees($curAjout);
		*/
	}
	
	function afficherTexteFinal(){
		global $textFinal;
		echo $textFinal;
	}
	
	function remplirDernierSponsor() {
		global $conn, $numSpon;
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
			if (($numSpon == 'null') || ($numSpon != $sponsor['N_EQUIPE']))
				echo '<option value="'. $sponsor['N_EQUIPE'] .'">'. $sponsor['NOM'] .'</option>';
			else
				echo '<option value="'. $sponsor['N_EQUIPE'] .'" selected>'. $sponsor['NOM'] .'</option>';
		}
			
	}

	function recupAnnee(){
		if(!empty($_POST['dateC'])){
			$dateC = $_POST['dateC'];
			return intval($dateC);
		}
		return null;
	}
	
	function peutRemplir() {
		if (!isset($_POST['verifier']) || (isset($_POST['verifier']) && $_POST['sponsor']!= 'null')) {
			echo 'readonly=""';
		}
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