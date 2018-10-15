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
		include("../html/affichageCoureur.html");
		
		/*$login = 'ETU2_49';
		$mdp = 'ETU2_49';
		$db = fabriquerChaineConnexion();
		
		$conn = OuvrirConnexion($db,$login,$mdp);
		$req = 'SELECT * FROM vt_coureur order by nom';
		$nbLignes = LireDonnees1($conn,$req,$tab);*/
		
		// Remplissage des info si coureur séléctionné :
		
		
		function afficheNom() {
			if (!empty($_POST)) {
				echo '<p>Nom : Prunier</p>';
			}
		}
		
		function affichePrenom() {
			if (!empty($_POST)) {
				echo '<p>Prenom : Bastien</p>';
			}
		}
		
		function listeCoureurs($tab,$nbLignes) {
			/*
			for ($i=0; $i<$nbLignes; $i++) {
				$tab[$i]["PRENOM"] = utf8_encode($tab[$i]["PRENOM"]);
				echo '<option value="'.$tab[$i]["N_COUREUR"].'">'.$tab[$i]['NOM'].' '.$tab[$i]['PRENOM'];
				echo '</option>';
			}*/
			echo '<option value="7">Bastien PRUNIER</option>';
			echo '<option value="7">Clément CATEL</option>';
			echo '<option value="7">Jérémy LAMY</option>';
			echo '<option value="7">Noe GUILLOUET</option>';
		}
		?>
	</body>
</html>