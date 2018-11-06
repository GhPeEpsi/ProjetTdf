<?php
	include ("pdo_oracle.php");
	include ("util_affichage.php");
	include ("../html/navBar.html");
	
	/*Serveur UNICAEN*/
	$login = 'ETU2_49';
	$mdp = 'ETU2_49';
	$db = "oci:dbname=spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr;charset=AL32UTF8";
	$conn = OuvrirConnexion($db,$login,$mdp);
	
	/*Bastien Localhost
	$login = 'copie_tdf_copie';
	$mdp = 'copie_tdf_copie';
	$db = fabriquerChaineConnexion2();
	$conn = OuvrirConnexion($db,$login,$mdp);
	*/

	$n_coureur;
	$nom;
	$prenom;
	
	if (!empty($_GET['numCoureur'])) {
		$n_coureur = intval($_GET['numCoureur']);
	}
	
	
	// --n_coureur, nom, prenom, nation, ... :
	$reqBase = 
		"select n_coureur as \"Numero de coureur\", co.nom as \"Nom\", prenom as \"Prenom\", annee_naissance as \"Annee de naissance\", annee_prem as \"Annee de première\", na.nom as \"Nation\"
		from tdf_coureur co
		join tdf_app_nation using (n_coureur)
		join tdf_nation na using (code_cio)
		where n_coureur = ".$n_coureur;
	$nbLignesBase = LireDonnees1($conn,$reqBase,$tabBase);
	//$nom = $tabBase[0]['Nom'];
	//$prenom = $tabBase[0]['Prenom'];
	
	$reqAnneeParticipation = 
			"select annee from tdf_parti_coureur
			join tdf_coureur using (n_coureur)
			where n_coureur = ".$n_coureur."
			order by annee";
	$nbLignesAnnee = LireDonnees1($conn,$reqAnneeParticipation,$tabAnnee);
		
	//Nombre de participations plus les années
	if ($nbLignesAnnee != 0) {
		$prob = false; //permet de savoir si il y a eu un soucis de lecture de participation
	}
	else {
		$prob = true;
	}
		
	/*$req = 'SELECT * FROM tdf_coureur order by nom';
	$nbLignes = LireDonnees1($conn,$req,$tab);*/
	
	

	//PLACE D'ARRIVÉE A CHAQUE TOUR
	//Remplissage des info si coureur séléctionné :
	function afficheBase() {
		global $nbLignesBase, $tabBase;
		foreach ($tabBase[0] as $key => $ligne) {
			if ($key != "Numero de coureur") {
				echo "<p>$key : $ligne</p>";
			}
		}
	}
	
	function afficherNbParti() {
		global $nbLignesAnnee;
		echo "A participé $nbLignesAnnee fois au tdf<br>";
	}
	
	function afficheAnnee() {
		global $conn, $nbLignesAnnee, $tabAnnee, $n_coureur, $prob;
		
		$style = "style=\"border: 1px solid black;\"";
		if ($prob) {echo "<h3>Pas encore de participation</h3>";return;}
		
		echo "<table $style>";
		echo 
			"<tr $style>
				<th $style>Annee</th>
				<th $style>Place</th>
			</tr>";
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
					where n_coureur =".$n_coureur."
					and annee = " . $ligne['ANNEE'] . "
					group by annee, nom, prenom
				)";
			$place = LireDonneesCount($conn,$reqPlaceParticipation);
			if ($place == '0') {
				$reqAbandon = "select libelle, n_epreuve from tdf_coureur
								join tdf_parti_coureur using (n_coureur)
								join tdf_abandon using (n_coureur, annee)
								join tdf_typeaban using (c_typeaban)
								where annee = " . $ligne['ANNEE'] . "
								and n_coureur = " . $n_coureur;
				LireDonnees1($conn, $reqAbandon, $repAban);
				$place = $repAban[0]['LIBELLE'] . " à la " . $repAban[0]['N_EPREUVE'] . "e épreuve";
			}
			echo "
				<tr $style>
					<th $style>" . $ligne['ANNEE'] . "</th>
					<th $style>$place</th>
				</tr>";
		}
		echo "</table>";
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