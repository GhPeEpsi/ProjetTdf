<?php
	include ("pdo_oracle.php");
	include ("util_affichage.php");
	include ("../html/navBar.html");

	/*Serveur UNICAEN*/
	$login = 'ETU2_49';
	$mdp = 'ETU2_49';
	$db = "oci:dbname=spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr;charset=AL32UTF8";
	
	/*Localhost*/
	// $login = 'copie_tdf';
	// $mdp = 'copie_tdf';
	// $db = fabriquerChaineConnexion2();
	
	$conn = OuvrirConnexion($db,$login,$mdp);
	
	//PROGRAMME PRINCIPAL :
	//récupération de l'année si elle est sélectionné
	$annee;
	if (isset($_POST['verifier'])) {
		if (isset($_POST['annee']) && $_POST['annee'] != "pasBon") {
			$annee = $_POST['annee'];
		}
	}

	//REQUETE :
	$req = 'select co.nom as "Nom du Coureur", co.prenom as "Prenom du Coureur", sp.nom as "Nom du Sponsor" from tdf_coureur co
				join tdf_parti_coureur using (n_coureur)
				join tdf_sponsor sp using (n_equipe, n_sponsor)
				where annee = :annee
				order by sp.nom';
	$cur = preparerRequete($conn,$req);

	
	
	//FONCTIONS DE TRAITEMENT :
	
	
	//affichage du tableau :
	function affichage() {
		global $conn, $annee, $cur;
		
		if (isset($annee)) {
			//Pour avoir des bordures dans le tableau :
			$style = "style=\"border: 1px solid black; margin: auto\"";
			//Bind de l'année et récupération des réponses :
			ajouterParam($cur,':annee',$annee);
			$nbCoureur = LireDonneesPreparees($cur, $tabRes);
			
			//affichage de la premiere ligne de tableau :
			echo "<table $style>";
			echo "<tr>";
			foreach ($tabRes[0] as $key => $ligne) {
				echo "<th $style>$key</th>";
			}
			echo "</tr>";
			
			//affichage des informations :
			foreach ($tabRes as $ligne) {
				echo "<tr>";
				foreach($ligne as $col) {
					echo "<th $style>$col</th>";
				}
				echo "</tr>";
			}
			echo "</table>";
		}
		else {
			echo "Selectionnez une année !";
		}
	}
	
	//LE FICHIER HTML:
	include("../html/affichageCoureurSponsor.html");

?>