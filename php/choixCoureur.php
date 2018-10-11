<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<title>Choix d'un coureur</title>
	</head>
	<body>
		<?php
		include ("pdo_oracle.php");
		include ("util_affichage.php");
	
		$login = 'copie_tdf_copie';
		$mdp = 'copie_tdf_copie';
		$db = 'oci:dbname=localhost:1521/xe';
		
/*		$login = "ETU2_33";
		$mdp = "ETU2_33";
		$db = fabriquerChaineConnexion();	*/
	
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
			include("../html/choixCoureur.html");
		}
		
		function listeCoureurs($tab,$nbLignes) {
			for ($i=0; $i<$nbLignes; $i++) {
				$tab[$i]["PRENOM"] = utf8_encode($tab[$i]["PRENOM"]);
				echo '<option value="'.$tab[$i]["N_COUREUR"].'">'.$tab[$i]['NOM'].' '.$tab[$i]['PRENOM'];
				echo '</option>';
			}
		}
		?>
	</body>
</html>