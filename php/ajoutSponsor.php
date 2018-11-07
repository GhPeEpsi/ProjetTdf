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


	//connection de Jérémy qui resté là après la merge
	// $db_username = 'copie_tdf_copie';
	// $db_password = 'copie_tdf_copie';
	// $db = "oci:dbname=localhost:1521/xe;charset=AL32UTF8";
	//$db = fabriquerChaineConnexion();


	 // $db_username = 'projet_php';
	 // $db_password = 'projet_php';
	 // $db = "oci:dbname=localhost:1521/xe;charset=AL32UTF8";
	$conn = OuvrirConnexion($db,$db_username,$db_password);
	
	//traitement :
	//permet de sauvegarder les commentaires et de les afficher à la fin :
	$textFinal = "";
	//permet de reremplire ou pas les champs lors de rafraichissement
	$reaffichage = true;
	
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

	// condition pour que rien ne se passe si tout n'est pas rempli, sinon, ajout du coureur à la base grace à la requête
	if(isset($_POST['verifier'])){

		//verfication du bon remplissage des champs obligatoire :
		//Si on oublie les un des champs et ca passe pas :
		if (empty($_POST['nom']) || empty($_POST['nomAbrege']) || $_POST['nationalite'] == ''){
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
			
			if (!empty($_POST['sponsor'])) {
				$n_equipe = $_POST['sponsor'];
			}
			
			if ($textFinal == "") {
				enregistrementDonnées();
			}
		}
	}
	
	//print_r($_POST);
	

	//FUNCTION :
	function enregistrementDonnées() {
		global $conn, $annee, $nomAbrege, $nom, $n_equipe,$cio, $textFinal, $reaffichage;
		
		//vérification de l'absence de sponsor de meme nom code_cio et nom abrégé :
		$req = 'select count(*) from tdf_sponsor 
		where na_sponsor = :nomAbrege
		and nom = :nom
		and code_cio = :cio';
		$cur = preparerRequete($conn,$req);
		//éxecution
		ajouterParam($cur,':nomAbrege',$nomAbrege);
		ajouterParam($cur,':nom',$nom);
		ajouterParam($cur,':cio',$cio);
		LireDonneesPreparees($cur, $tab);
		
		//requete d'insertion dans la base :
		$reqAjout = 'Insert into tdf_sponsor(n_equipe, n_sponsor, nom, na_sponsor, code_cio, annee_sponsor)
				values(:n_equipe, (select max(n_sponsor) from tdf_sponsor)+1,:nom, :nomAbrege, :cio, :annee)';
		$curAjout = preparerRequete($conn,$reqAjout);
		
		//print_r($tab);
		
		if ($tab[0]['COUNT(*)'] == 0) {
			//echo "<h1>$dateC ; $nomAbrege ; $nom ; $n_equipe ; $cio</h1>";
			ajouterParam($curAjout,':annee',$annee);
			ajouterParam($curAjout,':nomAbrege',$nomAbrege);
			ajouterParam($curAjout,':nom',$nom);
			ajouterParam($curAjout,':n_equipe',$n_equipe);
			ajouterParam($curAjout,':cio',$cio);
			majDonneesPreparees($curAjout);
			$textFinal = $textFinal . "Sponsor bien enregistré pour l'équipe $n_equipe !!";
			$reaffichage = $false;
		}
	}
	
	function afficherTexteFinal(){
		global $textFinal;
		echo $textFinal;
	}
	
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
		
		echo '<option value="null">Choisir un sponsor a mettre à jour</option>';
		
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

	function afficherNom(){
		global $nom, $reaffichage;
		if ($reaffichage)
			echo $nom;
	}

	function afficherDateC(){
		global $annee, $reaffichage;
		if ($reaffichage)
			echo $annee;
	}

	function afficherNomAbrege(){
		global $nomAbrege, $reaffichage;
		if ($reaffichage)
			echo $nomAbrege;
	}
	
	if (empty($GET)) {
		include ("../html/ajoutSponsor.html");
	}

?>