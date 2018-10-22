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
		
		/*Serveur UNICAEN*/
		$login = 'ETU2_49';
		$mdp = 'ETU2_49';
		$db = "oci:dbname=spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr;charset=AL32UTF8";
		$conn = OuvrirConnexion($db,$login,$mdp);
		/**/
		/*Bastien Localhost
		$login = 'projet_php';
		$mdp = 'projet_php';
		$db = fabriquerChaineConnexion2();
		$conn = OuvrirConnexion($db,$login,$mdp);
		*/
		
				

		//print_r($tabParticipation);
		
		//
		function afficheListe() {
			global $conn;
			
			// recherche des coureurs n'ayant jamais participé au tdf :
			$reqBase = 
				"select n_coureur, nom, prenom from tdf_coureur
				where n_coureur not in 
				(
					select n_coureur from tdf_parti_coureur
				)
				order by n_coureur";
			$nbcoureurs = LireDonnees1($conn,$reqBase,$tabParticipation);
			
			if ($nbcoureurs == 0)
				echo "<h3>Vous ne pouvez pas supprimer de coureur pour le moment ils ont tous participé à au moins 1 tdf</h3>";
			else 
				afficherSelect($tabParticipation);
		}
		
		function afficherSelect($tab) {
			echo "<select name=\"listeSupp\">";
			echo "<option value=\"none\">Choisir un coureur</option>";
			foreach ($tab as $coureur)
				echo "<option value=\"".$coureur['N_COUREUR']."\">".$coureur['NOM']. " ". $coureur['PRENOM']. "</option>";
			echo "</select>";
			echo "<input type=\"submit\" name=\"supp\" value=\"Supprimer\">";
		}
		
		if (isset($_POST['supp'])) {
			$n_coureur = $_POST['listeSupp'];
			if ($n_coureur != "none") {
				supprimer($n_coureur);
			}
			else {
				echo "<p>Veuillez choisir un coureur valide !</p>";
			}
		}
		
		function supprimer($n){
			global $conn;
			$reqAppNation = "delete from tdf_app_nation where n_coureur=".$n;
			$reqCoureur = "delete from tdf_coureur where n_coureur=".$n;
			majDonnees($conn,$reqAppNation);
			majDonnees($conn,$reqCoureur);
			echo "<p>Coureur bien enlevé de la base !</p>";
		}
		
		//Le fichier html:
		include("../html/supprimerCoureur.html");
		?>
	</body>
</html>