<?php
	include ("pdo_oracle.php");
	include ("util_affichage.php");

	
	// connexion à la base

	$login = 'copie_tdf_copie';
	$mdp = 'copie_tdf_copie';
	$db = 'oci:dbname=localhost:1521/xe';

	$conn = OuvrirConnexion($db,$login,$mdp);

	// pour récupérer l'année entrée par l'utilisateur
	// if(isset($_GET['dateN'])){
	// 	//echo 'on est dans le php';
	// 	$dateN = $_GET['dateN'];
	// 	$tab1 = explode("-", $dateN);
	// 	$annee = $tab1[0];
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
				echo '<span><font color="red">Veuillez entrer une date !</font></span>';
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