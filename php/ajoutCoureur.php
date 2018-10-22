<?php
	include ("pdo_oracle.php");
	include ("util_affichage.php");
	include ("verificationsForm.php");
	//echo '<meta charset="utf-8">';
	// connexion à la base
	$db_username = 'ETU2_49';
	$db_password = 'ETU2_49';
	$db = "oci:dbname=spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr;charset=AL32UTF8";
	//$db = fabriquerChaineConnexion();
	 // $db_username = 'copie_tdf_copie';
	 // $db_password = 'copie_tdf_copie';
	 // $db = fabriquerChaineConnexion2();

	$conn = OuvrirConnexion($db,$db_username,$db_password);
	//$conn->exec("set names AL32UTF8");

	//récupérer seulement l'annee de la date entrée
	if(isset($_GET['dateN'])){
		$dateN = $_GET['dateN'];
		$tab1 = explode("-", $dateN);
		$annee = $tab1[0];
		echo $annee;
	}

	$req = 'SELECT code_cio, nom FROM tdf_nation where annee_disparition is null order by nom';
	$nbLignes = LireDonnees1($conn,$req,$tab);
	// condition pour que rien ne se passe si tout n'est pas rempli, sinon, ajout du coureur à la base grace à la requête
	
	if(isset($_POST['verifier'])){

		if (empty($_POST['Nom']) || empty($_POST['prenom']) || $_POST['nationalite'] == 'Nationalité'){ //!isset($_POST['dateN']) || || !verifDepuisQ(recupAnnee())
		
			echo "<script> alert('vous n\'avez pas tout rempli') </script>";

		}else{
			//Changement de la valeur de l'element hidden :
			
			
			//BLOC 1
		
			$nom = $_POST['Nom'];
			$prenom = $_POST['prenom'];

			$nom = testNom($nom, $regex);
			$prenom = testPrenom($prenom, $regex);

			if($nom == NULL || $prenom == NULL){
				
				if($nom == NULL){
					echo "<script> alert('Le nom entré n\'est pas valide, recommencer'); </script>";
					//echo "<br>";
				}
				
				if($prenom == NULL){
					echo "<script> alert('Le prenom entré n\'est pas valide, recommencer'); </script>";
					//echo "<br>";
				}

			}else{

				$annee_naissance = recupAnnee();

				//requête pour ajouter un coureur à la base.
				$sql = "INSERT INTO tdf_coureur(n_coureur, nom, prenom, annee_naissance) VALUES ((select max(n_coureur) from tdf_coureur) + 1, :nom, :prenom, :annee_naissance)";

				$cur = preparerRequete($conn,$sql);
				//AfficherTab($cur);
				//FIN BLOC 1


				//BLOC 2 
				ajouterParam($cur,':nom',$nom);
				ajouterParam($cur,':prenom',$prenom);
				ajouterParam($cur,':annee_naissance',$annee_naissance);
				$res = majDonneesPreparees($cur);
				//AfficherTab($res);
				//FIN BLOC 2

				$nat = ajoutSelection();
				$depuisQuand = $_POST['depuisQ'];

				//requête pour ajouter annee_debut à la table tdf_app_nation en fonction du depuisQ rentré
				$sql2 = "INSERT INTO tdf_app_nation(n_coureur, code_cio,annee_debut) VALUES ((select max(n_coureur) from tdf_coureur),:nat, :depuisQuand)";

				$cur = preparerRequete($conn,$sql2);
				//AfficherTab($cur);
				//FIN BLOC 1

				//BLOC 2 
				ajouterParam($cur,':nat',$nat);
				ajouterParam($cur,':depuisQuand',$depuisQuand);
				$res = majDonneesPreparees($cur);
				//AfficherTab($res);

				echo "Vous avez inséré le coureur ".$nom. " " .$prenom." de nationalité ".$nat;
			}
		}
	}
	
	//permet d'aller voir les infos d'un coureur qui vient d'être entré :
	if(isset($_POST['regarder'])){
		$sql3 = "SELECT max(n_coureur) as max from tdf_coureur";
		LireDonnees1($conn,$sql3,$tab3);
		header ("location:affichageCoureur.php?numCoureur=".$tab3[0]['MAX']);
	}

	function recupAnnee(){
		if(isset($_POST['dateN'])){
			 	$dateN = $_POST['dateN'];
	 			$tab1 = explode("-", $dateN);
	 			$annee_naissance = $tab1[0];
	 			return $annee_naissance;
	 		}
	 	return null;
	}

	
	// On remplis la liste deroulante avec les nationalité de la base

	function remplirOption($tab,$nbLignes) 
	{
		for ($i=0; $i<$nbLignes; $i++)
		 {
			$tab[$i]["NOM"] = utf8_encode($tab[$i]["NOM"]);
			echo '<option value="'.$tab[$i]['CODE_CIO'].'">'.$tab[$i]['NOM'];
			echo '</option>';
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

	function verifDepuisQ($annee_naissance){
		if(!empty($_POST['depuisQ']) && !empty($annee_naissance)){
			if($_POST['depuisQ'] < $annee_naissance){
				echo "Vérifier que l'année entrée est inférieure à l'année de naissance";
				unset($_POST['depuisQ']);
				return FALSE;
			}
		}
		return TRUE;
	}

	if(empty($_GET)){
		include ("../html/ajoutCoureur.html"); //on inclut le fichier html
	}
	?>