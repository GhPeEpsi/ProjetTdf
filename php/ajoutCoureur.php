<?php
	include ("pdo_oracle.php");
	include ("util_affichage.php");

	
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


	$req = 'SELECT code_cio, nom FROM vt_nation where annee_disparition is null order by nom';
	$nbLignes = LireDonnees1($conn,$req,$tab);

	if ($conn)
	{	

		if(isset($_POST['verifier'])){

			if (empty($_POST['Nom']) || empty($_POST['prenom']) || !isset($_POST['dateN']) || !isset($_POST['nationalite']) || empty($_POST['depuisQ']) || !verifDepuisQ(recupAnnee())){
			
				echo "il faut tout remplir";

			}else{

				echo ("<hr/> BLOC 1 <br/>");
				$sql = "INSERT INTO vt_coureur(n_coureur, nom, prenom, annee_naissance) VALUES ((select max(n_coureur) from vt_coureur) + 1, :nom, :prenom, :annee_naissance)";

				if(!empty($_POST['Nom'])){
					$nom = $_POST['Nom'];
					echo "Nom ".$_POST['Nom']." sélectionné";
					echo "<br>";
				}else{
					echo '<span><font color="red">Veuillez entrer un nom !</font></span>';
					echo "<br>";
				}

				if(!empty($_POST['prenom'])){
					$prenom = $_POST['prenom'];
					echo "Prénom ".$prenom." sélectionné";
					echo "<br>";
				}else{
					echo '<span><font color="red">Veuillez entrer un prénom !</font></span>';
					echo "<br>";
				}

				$annee_naissance = recupAnnee();

				$cur = preparerRequete($conn,$sql);
				AfficherTab($cur);
				echo ("FIN BLOC 1 <hr/>");

				echo ("<hr/> BLOC 2 <br/>");
				ajouterParam($cur,':nom',$nom);
				ajouterParam($cur,':prenom',$prenom);
				ajouterParam($cur,':annee_naissance',$annee_naissance);
				$res = majDonneesPreparees($cur);
				AfficherTab($res);
				echo ("FIN BLOC 2 <hr/>");
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
					echo ("Nationalité $nat sélectionnée");
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