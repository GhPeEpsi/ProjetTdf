<?php
	include ("pdo_oracle.php");
	include ("util_affichage.php");

	
	// connexion à la base

	$db_username = 'copie_tdf_copie';
	$db_password = 'copie_tdf_copie';
	$db = fabriquerChaineConnexion2();

	$conn = OuvrirConnexion($db,$db_username,$db_password);

	// pour récupérer l'année entrée par l'utilisateur
	if(isset($_POST['dateN'])){
	 	$dateN = $_POST['dateN'];
	 	$tab1 = explode("-", $dateN);
	 	$annee = $tab1[0];
	 }
	// $req = "select code_cio, nom from tdf_nation
	// 	where annee_creation <$annee
	// 	and annee_disparition >$annee
	// 	UNION
	// 	select code_cio, nom from tdf_nation
	// 	where annee_creation is null
	// 	and annee_disparition is null";
	//$conn = OuvrirConnexion($db,$login,$mdp);
	$req = 'SELECT code_cio, nom FROM vt_nation where annee_disparition is null order by nom';
	$nbLignes = LireDonnees1($conn,$req,$tab);

	if ($conn)
	{	
		if(isset($_POST['verifier'])){

			echo ("<hr/> BLOC 1 <br/>");
			$sql = "INSERT INTO vt_coureur(n_coureur, nom, prenom) VALUES ((select max(n_coureur) from vt_coureur) + 1,:nom,:prenom)";

			if(isset($_POST['Nom'])){
				$nom = $_POST['Nom'];
			}
			if(isset($_POST['prenom'])){
				$prenom = $_POST['prenom'];
			}

			$cur = preparerRequete($conn,$sql);
			AfficherTab($cur);
			echo ("FIN BLOC 1 <hr/>");

			echo ("<hr/> BLOC 2 <br/>");
			// solution 1
			//ajouterParamPDO($cur,':val',$val,'entier',0);
			//ajouterParam($cur,':n_coureur',$n_coureur);
			ajouterParam($cur,':nom',$nom);
			ajouterParam($cur,':prenom',$prenom);
			$res = majDonneesPreparees($cur);
			AfficherTab($res);
			echo ("FIN BLOC 2 <hr/>");
		}
	}	
	// function ajoutALaBase(){

	// 	global $conn;

	// 	if(isset($_POST['verifier'])){

	// 		if(isset($_POST['Nom']))
	// 			$nom = $_POST['Nom'];
	// 		else
	// 			$nom = null;

	// 		if(isset($_POST['prenom']))
	// 			$prenom = $_POST['prenom'];
	// 		else
	// 			$prenom = null;
			
	// 		if(isset($_POST['dateN']))
	// 			$dateN = $_POST['dateN'];
	// 		else
	// 			$dateN = null;
			

			//$sql = 'select * from vt_coureur order by n_coureur';
			// $sql = "insert into tdf_coureur (n_coureur, nom) values (3500, 'CATEL')";//, :prenom, :annee)';
	
			//LireDonnees1($conn,$sql,$tab);
			//print_r($tab);
			/*$cur = preparerRequete($conn,$sql);
			AfficherTab($cur);
			//AfficherTab($cur);
			//$tab = array (
				//':nom'=>$nom//,
				// ':prenom'=>$prenom,
				// ':dateN'=>$dateN
			//);
			
			//$res = majDonneesPrepareesTab($cur,$tab);
			//echo $nom;
			//echo $res;
			//AfficherTab($res);
			ajouterParam($cur,':nom',$nom);
			// ajouterParamPDO($cur,':type',$type);
			// ajouterParamPDO($cur,':couleur',$couleur);
			$res = majDonneesPreparees($cur);
			AfficherTab($cur);
			AfficherTab($res);
			echo "on est arrivé jusque là ???";*/
	// 		$stmt = majDonnees($conn,$sql);
	// 		AfficherTab($stmt);
	// 	}
	// }


	//$nbLignes = LireDonnees1($conn,$req,$tabNation);
	//AfficherDonneeNation($tabNation,$nbLignes);
	//}

	// requete permettant d'afficher les nations en fonction de l'année entrée par l'utilisateur
	

	//On vérifie que le nom est séléctionné

	function ajoutNom(){
		if(isset($_POST['Nom'])){
			$nom = $_POST['Nom'];
			if(empty($nom)){
				echo '<span><font color="red">Veuillez entrer un nom !</font></span>';
				echo "</br>";
			}
			else{
				echo "Nom ".$_POST['Nom']." sélectionné";
				echo "</br>";
			}
		}
	}


	// function AfficherDonneeNation($tabNation,$nbLignes) {
	// 	if ($nbLignes > 0) {
	// 		for ($i = 0; $i < $nbLignes; $i++) { // balayage de toutes les lignes
	// 			echo $tabNation[$i]['NOM'];
	// 			echo '<br>';
	// 		}
	// 	}
	// }

	//On vérifie que le prénom est séléctionné

	function ajoutPrenom(){
		if(isset($_POST['prenom'])){
			$prenom = $_POST['prenom'];
			if(empty($prenom)){
				echo '<span><font color="red">Veuillez entrer un prénom !</font></span>';
				echo "</br>";
			}
			else{
				echo "Prénom ".$prenom." sélectionné";
				echo "</br>";
			}
		}
	}

	function ajoutDate(){
		if(isset($_POST['dateN'])){
			$dateN = $_POST['dateN'];
			if(empty($dateN)){
				echo '<span><font color="red">Veuillez entrer une date de naissance !</font></span>';
				echo "</br>";
			}
			else{
				echo "Date ".$dateN." sélectionnée";
				echo "</br>";
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

	if(empty($_GET)){
		include ("../html/ajoutCoureur.html"); //on inclut le fichier html
	}
	?>