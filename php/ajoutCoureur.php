<?php
	include ("pdo_oracle.php");
	include ("util_affichage.php");
	include ("verificationsForm.php");
	//echo '<meta charset="utf-8">';
	
	// connexion à la base
	//$db_username = 'ETU2_49';
	//$db_password = 'ETU2_49';
	//$db = "oci:dbname=spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr;charset=AL32UTF8";

	//connection de je sais pas qui resté là après la merge
	/*$db_username = 'copie_tdf_copie';
	$db_password = 'copie_tdf_copie';
	$db = "oci:dbname=localhost:1521/xe;charset=AL32UTF8";*/
	//$db = fabriquerChaineConnexion();
	
	$db_username = 'copie_tdf';
	$db_password = 'copie_tdf';
	$db = fabriquerChaineConnexion2();
	$conn = OuvrirConnexion($db,$db_username,$db_password);
	//$conn->exec("set names AL32UTF8");

	//récupérer seulement l'annee de la date entrée
	if(isset($_GET['dateN'])){
		$dateN = $_GET['dateN'];
		$tab1 = explode("-", $dateN);
		$annee = $tab1[0];
		echo $annee;
	}

	if(isset($_POST['Nom'])){
		$nom = $_POST['Nom'];
		$nom = testNom($nom, $regex);
	}
	
	if(isset($_POST['prenom'])){
		$prenom = $_POST['prenom'];
		$prenom = testPrenom($prenom, $regex);
	}

	$nat = ajoutSelection();

	if(!empty($_POST['depuisQ'])){
		$depuisQuand = $_POST['depuisQ'];
	}

	if(isset($_POST['dateN'])){
		$dateNaissance = $_POST['dateN'];
	}

	$req = 'SELECT code_cio, nom FROM tdf_nation where annee_disparition is null order by nom';
	$nbLignes = LireDonnees1($conn,$req,$tab);
	// condition pour que rien ne se passe si tout n'est pas rempli, sinon, ajout du coureur à la base grace à la requête
	
	if(isset($_POST['verifier'])){

		if (empty($_POST['Nom']) || empty($_POST['prenom']) || $_POST['nationalite'] == ''){ //!isset($_POST['dateN']) || || !verifDepuisQ(recupAnnee())
			echo "<script> alert('vous n\'avez pas tout rempli') </script>";

		}else{
			//Changement de la valeur de l'element hidden :
			
		

			//BLOC 1

			if($nom == NULL || $prenom == NULL){
				
				if($nom == NULL){
					echo "<script> alert('Le nom entré n\'est pas valide, recommencer'); </script>";
					//echo $prenom;

					//$_POST['nationalite'] = $nat;
					//echo "<br>";
				}
				
				if($prenom == NULL){
					echo "<script> alert('Le prenom entré n\'est pas valide, recommencer'); </script>";
					//echo $nom;
					//$_POST['Nom'] = $nom;
					//$_POST['nationalite'] = $nat;
					//echo "<br>";
				}

			}else{

				if((!empty($_POST['depuisQ']) && isset($_POST['dateN'])) || (isset($_POST['dateN']) && empty($_POST['depuisQ']))){
					if(empty($_POST['depuisQ'])){
						$annee_naissance = recupAnnee();
						$depuisQuand = $annee_naissance;
						echo "Vous devez remplir depuis quand si vous insérez une date de naissance";
					}else{
						$annee_naissance = recupAnnee();
						$depuisQuand = (int)$depuisQuand;
						$annee_naissance = (int)$annee_naissance;
						// echo $annee_naissance;
						// echo $depuisQuand;
						// gettype($depuisQuand);
						// gettype($annee_naissance);

						if((int)$depuisQuand < (int)$annee_naissance){
							echo "Vérifier que l'année entrée est inférieure à l'année de naissance";
						}else{


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
			}
		}
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
		global $nat;
		for ($i=0; $i<$nbLignes; $i++)
		 {
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

	function verifDepuisQ(){
		global $annee_naissance;
		if(!empty($_POST['depuisQ']) && !empty($annee_naissance)){
			if((int)$_POST['depuisQ'] < (int)$annee_naissance){
				echo "Vérifier que l'année entrée est inférieure à l'année de naissance";
				return FALSE;
			}else{
				echo "bite";
				return TRUE;
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



	if(empty($_GET)){
		include ("../html/ajoutCoureur.html"); //on inclut le fichier html
	}
	?>