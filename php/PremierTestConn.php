<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<title>Test de connexion + Liste déroulante</title>
	</head>
	<body>
		<?php
		include ("pdo_oracle.php");
		include ("util_affichage.php");
	
		$login = 'ETU2_49';
		$mdp = 'ETU2_49';
		$db = fabriquerChaineConnexion();
		
		$conn = OuvrirConnexion($db,$login,$mdp);
		$req = 'SELECT * FROM tdf_coureur order by nom';
		$nbLignes = LireDonnees1($conn,$req,$tab);
		AfficherDonnee2($tab);
		
		?>
	</body>
</html>