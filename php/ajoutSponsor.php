<?php
	if(empty($_GET)) {
		include ("../html/navBar.html");
	}
	include ("pdo_oracle.php");
	include ("util_affichage.php");
	include ("verificationsForm.php");

	
	// connexion à la base
	$db_username = 'ETU2_49';
	$db_password = 'ETU2_49';
	$db = "oci:dbname=spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr;charset=AL32UTF8";

	/*LocalHost*/
	// $db_username = 'copie_tdf';
	// $db_password = 'copie_tdf';
	// $db = "oci:dbname=localhost:1521/xe;charset=AL32UTF8";
	
	$conn = OuvrirConnexion($db,$db_username,$db_password);
	
	//traitement :
	//permet de sauvegarder les commentaires et de les afficher à la fin :
	$textFinal = "";
	//permet de reremplire ou pas les champs lors de rafraichissement
	$reaffichage = true;
	
	// condition pour que rien ne se passe si tout n'est pas rempli, sinon, ajout du coureur à la base grace à la requête
	if(isset($_POST['verifier'])){
		//verfication du bon remplissage des champs obligatoire :
		//Si on oublie l'un des champs ca passe pas :
		if (empty($_POST['nom']) || empty($_POST['nomAbrege']) || $_POST['nationalite'] == '' || $_POST['sponsor']=="null"){
			$textFinal = $textFinal."<br> Vous n'avez pas tout rempli";
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
					$annee = recupAnnee();
				}
			}
			
			$cio = recupNation();
			
			if (!empty($_POST['sponsor'])) { //récupération du numero d'équipe
				$n_equipe = $_POST['sponsor'];
			}
			
			if ($textFinal == "") { //s'il n'y a pas eu d'erreur (qui sont stokées dans textFinal) alors on envoie :
				enregistrementDonnées();
			}
		}
	}
	

	//FUNCTION :
	function enregistrementDonnées() {
		global $conn, $annee, $nomAbrege, $nom, $n_equipe,$cio, $textFinal, $reaffichage;
		
		//vérification de l'absence de sponsor de meme nom, code_cio et nom abrégé :
		$req = 'select count(*) from tdf_sponsor 
		where na_sponsor = :nomAbrege
		and nom = :nom
		and code_cio = :cio';
		$cur = preparerRequete($conn,$req);
		//execution
		ajouterParam($cur,':nomAbrege',$nomAbrege);
		ajouterParam($cur,':nom',$nom);
		ajouterParam($cur,':cio',$cio);
		LireDonneesPreparees($cur, $tab);
		
		//requete d'insertion dans la base :
		$reqAjout = 'Insert into tdf_sponsor(n_equipe, n_sponsor, nom, na_sponsor, code_cio, annee_sponsor)
				values(:n_equipe, (select max(n_sponsor) from tdf_sponsor)+1,:nom, :nomAbrege, :cio, :annee)';
		$curAjout = preparerRequete($conn,$reqAjout);
		
		if ($tab[0]['COUNT(*)'] == 0) { //si il y a 0 sponsor de meme parametre on ecrit dans la bdd
			//echo "<h1>$dateC ; $nomAbrege ; $nom ; $n_equipe ; $cio</h1>";
			ajouterParam($curAjout,':annee',$annee);
			ajouterParam($curAjout,':nomAbrege',$nomAbrege);
			ajouterParam($curAjout,':nom',$nom);
			ajouterParam($curAjout,':n_equipe',$n_equipe);
			ajouterParam($curAjout,':cio',$cio);
			majDonneesPreparees($curAjout);
			$textFinal = $textFinal . "Sponsor bien enregistré pour l'équipe $n_equipe !!";
			$reaffichage = false;
		}else{ //sinon on remplit le texte final pour dire deja existant :
			$textFinal = $textFinal . "Veuillez sélectionner un sponsor à mettre à jour. ";
		}
	}
	
	function afficherTexteFinal(){
		global $textFinal;
		echo $textFinal;
	}
	
	//remplit la liste déroulante pour le choix des sponsors a mettre à jour :
	function remplirDernierSponsor() {
		global $conn, $n_equipe, $reaffichage;
		$req = 'select n_equipe, n_sponsor, nom, na_sponsor, code_cio,annee_sponsor 
				from tdf_sponsor where (n_equipe, n_sponsor) in
				(
					select n_equipe, max(n_sponsor)
					from tdf_sponsor
					group by n_equipe
				)
				order by n_equipe';
		
		$nb = LireDonnees1($conn, $req, $tab);
		
		//première ligne :
		echo '<option value="null">Choisir un sponsor a mettre à jour</option>';
		
		//affichage de tout les sponsors :
		foreach ($tab as $sponsor) {
			if ($reaffichage) {
				if(($n_equipe == 'null') || ($n_equipe != $sponsor['N_EQUIPE']))
					echo '<option value="'. $sponsor['N_EQUIPE'] .'">'. $sponsor['NOM'] .'</option>';
				else
					echo '<option value="'. $sponsor['N_EQUIPE'] .'" selected>'. $sponsor['NOM'] .'</option>';
			}
			else
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
	function afficherNationalitées() {
		global $conn, $reaffichage;
		$cio = recupNation();
		
		//requete de récupération des nations :
		$req = 'SELECT code_cio, nom FROM tdf_nation where annee_disparition is null order by nom';  //A voir pour mettre dans la fonction remplir option au debut
		$nbLignes = LireDonnees1($conn,$req,$tab);
		
		foreach ($tab as $pays) {
			if (($cio == $pays['CODE_CIO']) && ($reaffichage)) {
				$pays["NOM"] = utf8_encode($pays["NOM"]);
				echo '<option value="'.$pays['CODE_CIO'].'" selected>'.$pays['NOM'];
				echo '</option>';
			}
			else{
				$pays["NOM"] = utf8_encode($pays["NOM"]);
				echo '<option value="'.$pays['CODE_CIO'].'">'.$pays['NOM'];
				echo '</option>';
			}
		}
	}
	
	function recupNation() {
		if (!empty($_POST)) {
			if (isset($_POST['nationalite'])) {
				$nat = $_POST['nationalite'];
				if($nat != "Nationalité"){
					return $nat;
				}
			}
		}
	}

	function afficherNom(){ //affiche le nom si on permet le ré-affichage
		global $nom, $reaffichage;
		if ($reaffichage)
			echo $nom;
	}

	function afficherDateC(){ //idem avec la date
		global $annee, $reaffichage;
		if ($reaffichage)
			echo $annee;
	}

	function afficherNomAbrege(){ //idem avec le nom abrege
		global $nomAbrege, $reaffichage;
		if ($reaffichage)
			echo $nomAbrege;
	}
	
	if (empty($GET)) {
		include ("../html/ajoutSponsor.html");
	}

?>