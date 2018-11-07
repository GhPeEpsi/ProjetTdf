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


	//$db_username = 'copie_tdf';
	//$db_password = 'copie_tdf';
	//$db = fabriquerChaineConnexion2();
	$conn = OuvrirConnexion($db,$db_username,$db_password);

	//récupérer l'annee entrée
	if(!empty($_GET['dateN'])){
		$dateN = $_GET['dateN'];
		echo $dateN;
	}

	if(!empty($_POST['Nom'])){
		$nom = $_POST['Nom'];
		$nom = testNom($nom, $regex);
	}

	if(!empty($_POST['prenom'])){
		$prenom = $_POST['prenom'];
		$prenom = testPrenom($prenom, $regex);
	}

	$nat = ajoutSelection();

	// if(!empty($_POST['depuisQ'])){
	// 	$depuisQuand = $_POST['depuisQ'];
	// }

	if(!empty($_POST['dateN'])){
		$dateNaissance = $_POST['dateN'];
	}

	$req = 'SELECT code_cio, nom FROM tdf_nation where annee_disparition is null order by nom';
	$nbLignes = LireDonnees1($conn,$req,$tab);
	
	if (isset($_POST['verifier1'])) {
		include "validerAjoutCoureur.php";
		// $textFinal = $textFinal.'<input type="submit" name="verifier" value="Valider" >';
	}
	// condition pour que rien ne se passe si tout n'est pas rempli, sinon, ajout du coureur à la base grace à la requête
	if(isset($_POST['verifier'])){

		//verfication du bon remplissage des champs obligatoire :
		if (empty($_POST['Nom']) || empty($_POST['prenom']) || $_POST['nationalite'] == ''){
			echo "<script> alert('vous n\'avez pas tout rempli') </script>";
		}else{
			//verification de la validité des nom, prenom :
			if($nom == NULL || $prenom == NULL){

				if($nom == NULL){
					//echo "<script> alert('Le nom entré n\'est pas valide, recommencer'); </script>";
				}

				if($prenom == NULL){
					//echo "<script> alert('Le prenom entré n\'est pas valide, recommencer'); </script>";
				}

			}else{

				if(!empty($_POST['dateN']) && empty($_POST['depuisQ'])){
					$verifInt = $_POST['dateN'];
					
					if(!ctype_digit($verifInt)|| $verifInt < 1900 || $verifInt > date('Y')){
						$textFinal = $textFinal."<br> Vous n'avez pas entré une date valide.";
					}else{
						$annee_naissance = recupAnnee();
						$depuisQuand = $annee_naissance;
						$sql = "INSERT INTO tdf_coureur(n_coureur, nom, prenom, annee_naissance) VALUES ((select max(n_coureur) from tdf_coureur) + 1, :nom, :prenom, :annee_naissance)";
						$cur = preparerRequete($conn,$sql);

						ajouterParam($cur,':nom',$nom);
						ajouterParam($cur,':prenom',$prenom);
						ajouterParam($cur,':annee_naissance',$annee_naissance);
						$passage = false;
						if (nonExistant()) {
							$res = majDonneesPreparees($cur);
							$passage = true;
						}
						
						//requête pour ajouter annee_debut à la table tdf_app_nation en fonction du depuisQ rentré
						$sql2 = "INSERT INTO tdf_app_nation(n_coureur, code_cio,annee_debut) VALUES ((select max(n_coureur) from tdf_coureur),:nat, :depuisQuand)";

						$cur = preparerRequete($conn,$sql2);
						ajouterParam($cur,':nat',$nat);
						ajouterParam($cur,':depuisQuand',$depuisQuand);
						if ($passage) {
							$res = majDonneesPreparees($cur);
						}

						if ($passage) {
							$textFinal = $textFinal."<br> Vous avez inséré le coureur ".$nom. " " .$prenom." de nationalité ".$nat;
						}
						else {
							$textFinal = $textFinal."<br> Le coureur existe deja veuillez changer le nom, le prenom, ou la nationalité";
						}
					}
				}elseif(!empty($_POST['dateN'])&& (!empty($_POST['depuisQ']))){
					$verifInt = $_POST['dateN'];
					if(!ctype_digit($verifInt)|| $verifInt < 1900 || $verifInt > date('Y')){
						$textFinal = $textFinal."<br> Vous n'avez pas entré une date valide";
					}else{
						$annee_naissance = recupAnnee();
						$depuisQuand = $annee_naissance;

						if($depuisQuand > $annee_naissance){
							$textFinal = $textFinal."<br> Vérifier que l'année entrée dans \"depuis Quand\" est inférieure à l'année de naissance";
						}else{
							//requête pour ajouter un coureur à la base.
							$sql = "INSERT INTO tdf_coureur(n_coureur, nom, prenom, annee_naissance) VALUES ((select max(n_coureur) from tdf_coureur) + 1, :nom, :prenom, :annee_naissance)";
							$cur = preparerRequete($conn,$sql);
							 
							ajouterParam($cur,':nom',$nom);
							ajouterParam($cur,':prenom',$prenom);
							ajouterParam($cur,':annee_naissance',$annee_naissance);
							$passage = false;
							if (nonExistant()) {
								$res = majDonneesPreparees($cur);
								$passage = true;
							}

							//requête pour ajouter annee_debut à la table tdf_app_nation en fonction du depuisQ rentré
							$sql2 = "INSERT INTO tdf_app_nation(n_coureur, code_cio,annee_debut) VALUES ((select max(n_coureur) from tdf_coureur),:nat, :depuisQuand)";

							$cur = preparerRequete($conn,$sql2);

							ajouterParam($cur,':nat',$nat);
							ajouterParam($cur,':depuisQuand',$depuisQuand);
							if ($passage) {
								$res = majDonneesPreparees($cur);
							}
							
							if ($passage) {
								$textFinal = $textFinal."<br> Vous avez inséré le coureur ".$nom. " " .$prenom." de nationalité ".$nat;
							}
							else {
								$textFinal = $textFinal."<br> Le coureur existe deja veuillez changer le nom, le prenom, ou la nationalité";
							}
						}
					}
				}elseif (empty($_POST['dateN']) && !empty($_POST['depuisQ'])) {
						$textFinal = $textFinal."<br> Vous devez entrer une année de naissance si vous remplissez le champs Depuis Quand.";
				}else{
					$annee_naissance = recupAnnee();
					$depuisQuand = null;
					//requête pour ajouter un coureur à la base.
					$sql = "INSERT INTO tdf_coureur(n_coureur, nom, prenom, annee_naissance) VALUES ((select max(n_coureur) from tdf_coureur) + 1, :nom, :prenom, :annee_naissance)";
					$cur = preparerRequete($conn,$sql); 
					ajouterParam($cur,':nom',$nom);
					ajouterParam($cur,':prenom',$prenom);
					ajouterParam($cur,':annee_naissance',$annee_naissance);
					
					$passage = false;
					if (nonExistant()) {
						$res = majDonneesPreparees($cur);
						$passage = true;
					}
					
					//requête pour ajouter annee_debut à la table tdf_app_nation en fonction du depuisQ rentré
					$sql2 = "INSERT INTO tdf_app_nation(n_coureur, code_cio,annee_debut) VALUES ((select max(n_coureur) from tdf_coureur),:nat, :depuisQuand)";

					$cur = preparerRequete($conn,$sql2);
					ajouterParam($cur,':nat',$nat);
					ajouterParam($cur,':depuisQuand',$depuisQuand);
					
					if ($passage) {
						$res = majDonneesPreparees($cur);
					}

					if ($passage) {
						$textFinal = $textFinal."<br> Vous avez inséré le coureur ".$nom. " " .$prenom." de nationalité ".$nat;
					}
					else {
						$textFinal = $textFinal."<br> Le coureur existe deja veuillez changer le nom, le prenom, ou la nationalité";
					}
				}
			}
		}
	}

	//FUNCTION :	
	
	function afficherTextFinal1(){
		global $textFinal1;
		echo $textFinal1;
	}	
	
	function afficherTextFinal(){
		global $textFinal;
		echo $textFinal;
	}

	//verifier que le coureur qu'on veut entrer n'existe pas deja :
	function nonExistant() {
		global $conn, $nom, $prenom, $nat;
		
		$req = 'select count(*) as nb from tdf_coureur
		where nom = \''.$nom.'\'
		and prenom = \''.$prenom.'\'';

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
				if($nat != ""){
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