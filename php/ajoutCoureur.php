<?php
	include ("pdo_oracle.php");
	include ("util_affichage.php");
	include ("verificationsForm.php");
	
	// connexion à la base

	$db_username = 'copie_tdf_copie';
	$db_password = 'copie_tdf_copie';
	$db = fabriquerChaineConnexion2();

	$conn = OuvrirConnexion($db,$db_username,$db_password);

	//récupérer seulement l'annee de la date entrée
	if(isset($_GET['dateN'])){
		$dateN = $_GET['dateN'];
		$tab1 = explode("-", $dateN);
		$annee = $tab1[0];
		echo $annee;
	}

	// condition pour que rien ne se passe si tout n'est pas rempli, sinon, ajout du coureur à la base grace à la requête
	if ($conn)
	{	

		if(isset($_POST['verifier'])){

			if (empty($_POST['Nom']) || empty($_POST['prenom']) || !isset($_POST['dateN']) || !isset($_POST['nationalite']) || empty($_POST['depuisQ']) || !verifDepuisQ(recupAnnee())){
			
				echo "<script> alert('vous n\'avez pas tout rempli') </script>";

			}else{
				//BLOC 1
			
				$nom = $_POST['Nom'];
				$prenom = $_POST['prenom'];

				$nom = testNom($nom, $regex);
				$prenom = testPrenom($prenom, $regex);

				if($nom == NULL || $prenom == NULL){
					
					if($nom == NULL){
						echo "Le nom entré n'est pas valide, recommencer";
						echo "<br>";
					}
					
					if($prenom == NULL){
						echo "Le prenom entré n'est pas valide, recommencer";
						echo "<br>";
					}

				}else{
					echo "Nom ".$nom." sélectionné";
					echo "<br>";
					echo "Prénom ".$prenom." sélectionné";
					echo "<br>";

					$annee_naissance = recupAnnee();

					//requête pour ajouter un coureur à la base.
					$sql = "INSERT INTO vt_coureur(n_coureur, nom, prenom, annee_naissance) VALUES ((select max(n_coureur) from vt_coureur) + 1, :nom, :prenom, :annee_naissance)";

					$cur = preparerRequete($conn,$sql);
					AfficherTab($cur);
					//FIN BLOC 1

					//BLOC 2 
					ajouterParam($cur,':nom',$nom);
					ajouterParam($cur,':prenom',$prenom);
					ajouterParam($cur,':annee_naissance',$annee_naissance);
					$res = majDonneesPreparees($cur);
					AfficherTab($res);
					//FIN BLOC 2

					$nat = ajoutSelection();
					$depuisQuand = $_POST['depuisQ'];

					//requête pour ajouter annee_debut à la table tdf_app_nation en fonction du depuisQ rentré
					$sql2 = "INSERT INTO tdf_app_nation(n_coureur, code_cio,annee_debut) VALUES ((select max(n_coureur) from tdf_coureur),:nat, :depuisQuand)";

					$cur = preparerRequete($conn,$sql2);
					AfficherTab($cur);
					//FIN BLOC 1

					//BLOC 2 
					ajouterParam($cur,':nat',$nat);
					ajouterParam($cur,':depuisQuand',$depuisQuand);
					$res = majDonneesPreparees($cur);
					AfficherTab($res);


				}
			}
		}
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

	function ajoutDate(){
		if(isset($_POST['dateN'])){
			$dateN = $_POST['dateN'];
			if(empty($dateN)){
				echo '<span><font color="red">Veuillez entrer une date de naissance !</font></span>';
				echo "<br>";
			}
			else{
				echo "Date ".$dateN." sélectionnée";
				echo "<br>";
			}
		}
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
				if($nat == "Nationalité"){
					echo '<span><font color="red">Veuillez sélectionner une Nationalité !</font></span>';
				}
				else{
					//echo ("Nationalité $nat sélectionnée");
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