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
	
		$login = 'copie_tdf';
		$mdp = 'copie_tdf_local';
		$db = 'oci:dbname=localhost:1521/xe';
		
		$conn = OuvrirConnexion($db,$login,$mdp);
		$req = 'SELECT DISTINCT code_cio FROM vt_app_nation order by code_cio';
		$nbLignes = LireDonnees1($conn,$req,$tab);
		
		if (!empty($_POST)) {
			if (isset($_POST['code_cio'])) {
				$nat = $_POST['code_cio'];
				echo ("Nationalité $nat sélectionné");
			}
		}
		else {
			include ("../html/ajoutCoureur.html");
		}
		?>   	
	</body>
</html>