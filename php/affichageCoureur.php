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
		
		/*Serveur UNICAEN
		$login = 'ETU2_49';
		$mdp = 'ETU2_49';
		$db = fabriquerChaineConnexion();
		$conn = OuvrirConnexion($db,$login,$mdp);
		*/
		/*Bastien Localhost*/
		$login = 'projet_php';
		$mdp = 'projet_php';
		$db = fabriquerChaineConnexion2();
		$conn = OuvrirConnexion($db,$login,$mdp);
		/**/
		
		// --n_coureur, nom, prenom, nation, ... :
		$reqBase = 
			"select n_coureur as \"Numero de coureur\", co.nom as \"Nom\", prenom as \"Prenom\", annee_naissance as \"Annee de naissance\", annee_prem as \"Annee de première\", na.nom as \"Nation\"
			from tdf_coureur co
			join tdf_app_nation using (n_coureur)
			join tdf_nation na using (code_cio)
			where co.nom = 'JALABERT'
			and prenom = 'Laurent'";
		$nbLignesBase = LireDonnees1($conn,$reqBase,$tabBase);
		
		//Nombre de participations plus les années
		$reqAnneeParticipation = 
			"select annee from tdf_parti_coureur
			join tdf_coureur using (n_coureur)
			where nom = 'JALABERT'
			and prenom = 'Laurent'
			order by annee";
		$nbLignesAnnee = LireDonnees1($conn,$reqAnneeParticipation,$tabAnnee);
			
		/*$req = 'SELECT * FROM tdf_coureur order by nom';
		$nbLignes = LireDonnees1($conn,$req,$tab);*/
		
		

		//PLACE D'ARRIVÉE A CHAQUE TOUR
		//Remplissage des info si coureur séléctionné :
		function afficheBase() {
			global $nbLignesBase, $tabBase;
			foreach ($tabBase[0] as $key => $ligne)
				echo "<p>$key : $ligne</p>";
		}
		
		function afficherNbParti() {
			global $nbLignesAnnee;
			echo "A participé $nbLignesAnnee fois au tdf<br>";
		}
		
		function afficheAnnee() {
			global $conn, $nbLignesAnnee, $tabAnnee;
			echo "annee : place<br>";
			foreach($tabAnnee as $ligne) {
				$reqPlaceParticipation = 
					"select count(tmp) as nb from
					(
						(
							select annee, nom, prenom, sum(total_seconde) as tmp from tdf_coureur co
							join tdf_parti_coureur using (n_coureur)
							join tdf_temps using (n_coureur, annee)
							where annee = " . $ligne['ANNEE'] . "
							group by annee, nom, prenom
						)
						minus
						(
							select annee, nom, prenom, sum(total_seconde) as tmp from tdf_coureur co
							join tdf_parti_coureur using (n_coureur)
							join tdf_abandon using (n_coureur, annee)
							join tdf_temps using (n_coureur, annee)
							where annee = " . $ligne['ANNEE'] . "
							group by annee, nom, prenom
						)
						order by tmp
					)
					where tmp <=
					(
						select sum(total_seconde) as tmp from tdf_coureur co
						join tdf_parti_coureur using (n_coureur)
						join tdf_temps using (n_coureur, annee)
						where nom = 'JALABERT'
						and prenom = 'Laurent'
						and annee = " . $ligne['ANNEE'] . "
						group by annee, nom, prenom
					)";
				$place = LireDonneesCount($conn,$reqPlaceParticipation);
				if ($place == '0') {
					$rep = $ligne['ANNEE'] . " : Abandon<br>";
				}
				else {
					$rep = $ligne['ANNEE'] . " : " . $place . "<br>";
				}
				echo $rep;
			}
		}
		
		//Le fichier html:
		include("../html/affichageCoureur.html");
		
		
		//à déplacer plus tard ...
		function LireDonneesCount($conn,$sql) {
			$cur = $conn->query($sql);
			$tab = $cur->fetchall(PDO::FETCH_ASSOC);
			return $tab[0]['NB'];
		}
		
		?>
	</body>
</html>