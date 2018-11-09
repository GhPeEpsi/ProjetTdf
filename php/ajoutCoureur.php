<?php
	
	//On inclut les fichiers nécéssaire.
	
	//On inclut la navBar de cette manière afin de ne pas "polluer" le champs "depuisQuand" par ce qui est écrit dans la navBar
	if(empty($_GET)) {
		include ("../html/navBar.html");
	}

	include ("pdo_oracle.php");
	include ("util_affichage.php");
	include ("verificationsForm.php");


	/* --------------------------------------------------------------------------------------------------------------------------------- */
	/* ----------------------------------------------------Connexion-------------------------------------------------------------------- */
	/* --------------------------------------------------------------------------------------------------------------------------------- */

	// connexion à la base
	 // $db_username = 'ETU2_49';
	 // $db_password = 'ETU2_49';
	 // $db = "oci:dbname=spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr;charset=AL32UTF8";


	//connection locale de Jérémy
	$db_username = 'copie_tdf_copie';
	$db_password = 'copie_tdf_copie';
	$db = "oci:dbname=localhost:1521/xe;charset=AL32UTF8";
	
	$conn = OuvrirConnexion($db,$db_username,$db_password);
	
	

	//permet de récupérer l'annee entrée grâce à la méthode GET pour la fonction chargerDate en javascript du fichier "ajoutCoureur.html".
	if(!empty($_GET['dateN'])){
		$dateN = $_GET['dateN'];
		echo $dateN;
	}

	//On initialise les variables si le champs n'est pas vide et on procède au test de validité du nom et du prenom via testNom/Prenom (fonction du fichier verificationForm.php).
	if(!empty($_POST['Nom'])){
		$nom = $_POST['Nom'];
		$nom = testNom($nom, $regex);
	}

	if(!empty($_POST['prenom'])){
		$prenom = $_POST['prenom'];
		$prenom = testPrenom($prenom, $regex);
	}

	//nat est récupéré via la fonction ajoutSelection qui procède avant à un test.
	$nat = ajoutSelection();


	if(!empty($_POST['dateN'])){
		$dateNaissance = $_POST['dateN'];
	}

	//Variable qui incrémentera tous les messages à afficher au fur et à mesure et qui va être affichée à la fin par la fonction "afficherTextFinal".
	$textFinal = "";

	//requete qui va permettre par la suite, l'entrée des nations dans le select correspondant.
	$req = 'SELECT code_cio, nom FROM tdf_nation where annee_disparition is null order by nom';
	$nbLignes = LireDonnees1($conn,$req,$tab);
	

	/* -------------------------------------------------------------------------------------------------------------------------------- */
	/* --------------------------------------------Début des conditions de vérification------------------------------------------------ */
	/* -------------------------------------------------------------------------------------------------------------------------------- */

	//Si l'utilisateur clique sur vérifier, alors on charge le fichier php correspondant à la vérification de ce que l'utilisateur a préalablement entré.
	if (isset($_POST['verifier1'])) {
		include "validerAjoutCoureur.php";
	}
	// première condition qui se déclenche si l'utilisateur clique sur vérifier.
	if(isset($_POST['verifier'])){

/* --------------------------------------------------------------------------------------------------------------------------------------------------------- */
/* ------------------------------------------- 1). verfication du bon remplissage des champs obligatoire : ------------------------------------------------- */
/*---------------------------------------------------------------------------------------------------------------------------------------------------------- */

		if (empty($_POST['Nom']) || empty($_POST['prenom']) || $_POST['nationalite'] == ''){
			echo "<script> alert('vous n\'avez pas tout rempli') </script>";
		}else{

/* --------------------------------------------------------------------------------------------------------------------------------------------------------- */
/* 2) a) vérification de la validité d'une année entrée. Si on remplit le champs "année de naissance" mais que l'on ne remplit pas le champs "Depuis quand". */
/*---------------------------------------------------------------------------------------------------------------------------------------------------------- */
			 
			if(!empty($_POST['dateN']) && empty($_POST['depuisQ'])){
				$verifInt = $_POST['dateN'];

				//Si l'annee entrée n'est pas un entier et que cet entier n'est pas entre 1900 et l'année actuelle alors on envoie un message d'erreur.
				if(!ctype_digit($verifInt)|| $verifInt <= 1900 || $verifInt > date('Y')){
					$textFinal = $textFinal."<br> Vous n'avez pas entré une date valide.";
				}else{
					//Sinon, on execute les requetes qui ajoute le coureur à la base et le depuis Quand dans la table tdf_app_nation.
					
					//On récupere cette année
					$annee_naissance = recupAnnee();
					//comme le champs depuisQuand etait vide, on le remplis automatiquement par l'année entrée dans Année de naissance
					$depuisQuand = $annee_naissance;

					//requête pour ajouter un coureur à la base.
					$sql = "INSERT INTO tdf_coureur(n_coureur, nom, prenom, annee_naissance) VALUES ((select max(n_coureur) from tdf_coureur) + 1, :nom, :prenom, :annee_naissance)";
					$cur = preparerRequete($conn,$sql);

					ajouterParam($cur,':nom',$nom);
					ajouterParam($cur,':prenom',$prenom);
					ajouterParam($cur,':annee_naissance',$annee_naissance);

					//On vérifie que le coureur n'est pas déjà dans la base
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
					
					//dernière condition de vérification de doublon et insertion ou envoie message erreur 
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

/* --------------------------------------------------------------------------------------------------------------------------------------------------------- */
/*------------- 2) b) vérification de la validité d'une année entrée. Si on remplit le champs "année de naissance" et le champs "Depuis quand".------------- */
/*---------------------------------------------------------------------------------------------------------------------------------------------------------- */
		 
			}elseif(!empty($_POST['dateN'])&& (!empty($_POST['depuisQ']))){
				$verifInt = $_POST['dateN'];
				//Si l'annee entrée n'est pas un entier et que cet entier n'est pas entre 1900 et l'année actuelle alors on envoie un message d'erreur.
				if(!ctype_digit($verifInt)|| $verifInt <= 1900 || $verifInt > date('Y')){
					$textFinal = $textFinal."<br> Vous n'avez pas entré une date valide";
				}else{
					//Sinon, on execute les requetes qui ajoute le coureur à la base et le depuis Quand dans la table tdf_app_nation.
					
					//On récupere cette année
					$annee_naissance = recupAnnee();
					$depuisQuand = $annee_naissance;

					//Si jamais l'année entrée dans "Depuis Quand" est supérieure à l'année entrée dans "Année de naissance", alors on envoie un message d'erreur. Ainsi on ajoute rien dans la base pour le moment.
					if($depuisQuand > $annee_naissance){
						$textFinal = $textFinal."<br> Vérifier que l'année entrée dans \"depuis Quand\" est inférieure à l'année de naissance";
					}else{
						//Sinon on fait les ajouts:
						//requête pour ajouter un coureur à la base.
						$sql = "INSERT INTO tdf_coureur(n_coureur, nom, prenom, annee_naissance) VALUES ((select max(n_coureur) from tdf_coureur) + 1, :nom, :prenom, :annee_naissance)";
						$cur = preparerRequete($conn,$sql);
						 
						ajouterParam($cur,':nom',$nom);
						ajouterParam($cur,':prenom',$prenom);
						ajouterParam($cur,':annee_naissance',$annee_naissance);

						//On vérifie que le coureur n'est pas déjà dans la base
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

						//dernière condition de vérification de doublon et insertion ou envoie message erreur
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

/* --------------------------------------------------------------------------------------------------------------------------------------------------------- */
/*------------------ 2) c) Si on ne remplit pas "Année de naissance" mais que l'on remplit "Depuis Quand", on renvoie un message d'erreur.------------------ */
/*---------------------------------------------------------------------------------------------------------------------------------------------------------- */

			}elseif (empty($_POST['dateN']) && !empty($_POST['depuisQ'])) {
					$textFinal = $textFinal."<br> Vous devez entrer une année de naissance si vous remplissez le champs Depuis Quand.";
			}
		}
	}

	/* -------------------------------------------------------------------------------------------------------------------------------- */
	/* --------------------------------------------------------- FONCTIONS ------------------------------------------------------------ */
	/* -------------------------------------------------------------------------------------------------------------------------------- */	
	
	//Fonction utilisée par le fichier "validerAjoutCoureur.php" et qui permet d'afficher le contenu de la variable textFinal1 qui incrémente tous les messages d'erreurs commises par un utilisateur
	function afficherTextFinal1(){
		global $textFinal1;
		echo $textFinal1;
	}
	
	//Fonction permettant d'afficher le contenu de la variable textFinal1 qui incrémente tous les messages d'erreurs commises par un utilisateur
	function afficherTextFinal(){
		global $textFinal;
		echo $textFinal;
	}

	//Fonction permettant de vérifier que le coureur qu'on veut entrer n'existe pas deja :
	function nonExistant() {
		global $conn, $nom, $prenom, $nat;
		
		$req = 'select count(*) as nb from tdf_coureur
		where nom = '.$conn->quote($nom).'
		and prenom = '.$conn->quote($prenom);
		
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

	//Fonction permet de remplir une balise Hidden pour permettre ou non d'aller consulter les informations du coureur que l'on vient d'entrer
	//si on vient de rentrer un coureur alors le droit de passage est vrai sinon il est faux.
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

	//Fonction permettant de récupérer l'année de naissance entrée par un utilisateur.
	function recupAnnee(){
		if(!empty($_POST['dateN'])){
			$annee_naissance = $_POST['dateN'];
			return intval($annee_naissance);
		}
		return null;
	}


	// On remplit la liste deroulante avec les nationalité de la base
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

	//insertion du fichier ajoutCoureur.html. On inclut le fichier de cette manière afin de ne pas "polluer" le champs "depuisQuand" par ce qui est écrit dans le fichier 
	if(empty($_GET)){
		include ("../html/ajoutCoureur.html");
	}
?>