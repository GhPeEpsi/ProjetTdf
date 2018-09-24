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
	
		$login = 'copie_tdf';
		$mdp = 'copie_tdf_local';
		$db = 'oci:dbname=localhost:1521/xe';
		
		$conn = OuvrirConnexion($db,$login,$mdp);
		$req = 'SELECT * FROM vt_coureur order by nom';
		$nbLignes = LireDonnees1($conn,$req,$tab);
		
		if (!empty($_POST)) {
			if (isset($_POST['coureur'])) {
				$cour = $_POST['coureur'];
				echo ("Coureur $cour sélectionné");
			}
		}
		else {
			include ("../html/PremierTestConn.html");
		}
		?>   	
	</body>
</html>