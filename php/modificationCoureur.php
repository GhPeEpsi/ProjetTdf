<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<title>Modification d'un coureur</title>
	</head>
	<body>
		<?php
		include ("pdo_oracle.php");
		include ("util_affichage.php");
		
		$login = 'ETU2_49';
		$mdp = 'ETU2_49';
		$db = fabriquerChaineConnexion();	
	
/*		$login = 'copie_tdf_copie';
		$mdp = 'copie_tdf_copie';
		$db = fabriquerChaineConnexion2();	*/
	
		$conn = OuvrirConnexion($db,$login,$mdp);
		$req = 'SELECT * FROM tdf_coureur order by nom';
		$nbLignes = LireDonnees1($conn,$req,$tab);
		
		include ("../html/modificationCoureur.html");
		
		function numeroCoureur($conn) {
			$req1 = 'SELECT n_coureur FROM tdf_coureur where nom = \'ABADIE\'';
			$nbLignes1 = LireDonnees1($conn,$req1,$tab);
			echo $tab[0]['N_COUREUR'];
		}
		
		function verificationEnvoi() {	
		}
		?>	
	</body>
</html>