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
	
		if (isset($_GET['coureur']))
			$cour = $_GET['coureur'];

		$login = 'copie_tdf_copie';
		$mdp = 'copie_tdf_copie';
	//	$db = 'oci:dbname=localhost:1521/xe';
		$db = 'localhost:1521/xe';
		$conn = OuvrirConnexion($db,$login,$mdp);
		
		if (isset($_GET['coureur'])) {
			echo "debut";
			$req = "SELECT nom, prenom FROM vt_coureur where nom like upper('".$cour."%')";
			$cur = ExecuterRequete($conn,$req);
			$nb = LireDonnees4($cur,$donnees);
			//AfficherDonnee2($donnees,$nb);
			echo "fin";
		}
		?>	
	</body>
</html>