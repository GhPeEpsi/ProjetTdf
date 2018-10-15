<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<title>Information d'un coureur</title>
	</head>
	<body>
		<?php
		include ("pdo_oracle.php");
		include ("util_affichage.php");
		
		$login = 'ETU2_49';
		$mdp = 'ETU2_49';
		$db = fabriquerChaineConnexion();
		$conn = OuvrirConnexion($db,$login,$mdp);
		
		// --n_coureur, nom, prenom, nation, ... :
		$reqBase = 
			"select n_coureur as "Numero de coureur", co.nom as "Nom", prenom as "Prenom", annee_naissance as "Annee de naissance", annee_prem as "Annee de première", na.nom as "Nation"
			from tdf_coureur co
			join tdf_app_nation using (n_coureur)
			join tdf_nation na using (code_cio)
			where co.nom = \'JOACHIM\'
			and prenom = \'Benoit\'";
		$nbLignesBase = LireDonnees1($conn,$reqBase,$tabBase);
		
		$reqNbParticipation = 
			"select count(*) from tdf_parti_coureur
			join tdf_coureur using (n_coureur)
			where nom = 'JOACHIM'
			and prenom = 'Benoit'";
		$nbLignesNb = LireDonnees1($conn,$reqNbParticipation,$tabNb);
			
		$req = 'SELECT * FROM tdf_coureur order by nom';
		$nbLignes = LireDonnees1($conn,$req,$tab);
		
		

		//PLACE D'ARRIVÉE A CHAQUE TOUR
		// Remplissage des info si coureur séléctionné :
		
		echo '<pre>';
		function afficheBase() {
			global $nbLignesBase, $tabBase;
			print_r($tabBase);
		}
		
		function afficheNb() {
			global $nbLignesNb, $tabNb;
			print_r($tabNb);
		}
		
		//Le fichier html:
		include("../html/affichageCoureur.html");
		?>
	</body>
</html>