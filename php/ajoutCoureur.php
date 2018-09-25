<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<title>Ajouter Coureur</title>
	</head>
	<body>
		<?php
		include ("pdo_oracle.php");
		include ("util_affichage.php");
	
		$login = 'copie_tdf_copie';
		$mdp = 'copie_tdf_copie';
		$db = 'oci:dbname=localhost:1521/xe';
		
		$conn = OuvrirConnexion($db,$login,$mdp);
		$req = 'SELECT code_cio, nom FROM vt_nation order by nom';
		$nbLignes = LireDonnees1($conn,$req,$tab);
		
		if (!empty($_POST)) {
			if (isset($_POST['nationalite'])) {
				$nat = $_POST['nationalite'];
				echo ("Nationalité $nat sélectionnée");
			}
		}
		else {
			include ("../html/ajoutCoureur.html");
		}
		?>   	
	</body>
</html>