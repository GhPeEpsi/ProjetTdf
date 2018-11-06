<?php
	include ("pdo_oracle.php");
	include ("verificationsForm.php");
	include ("../html/navBar.html");
	
	// connexion à la base
	// $db_username = 'ETU2_49';
	// $db_password = 'ETU2_49';
	// $db = "oci:dbname=spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr;charset=AL32UTF8";
	// $conn = OuvrirConnexion($db,$db_username,$db_password);
	
	$db_username = 'projet_php';
	$db_password = 'projet_php';
	$db = fabriquerChaineConnexion2();
	$conn = OuvrirConnexion($db,$db_username,$db_password);

	$reqSpon = 'select distinct tdf_nation.nom from tdf_nation 
			join tdf_sponsor using (code_cio) order by nom';
			
	$reqCour = 'select distinct tdf_nation.nom from tdf_nation 
			join tdf_app_nation using (code_cio)
			join tdf_coureur using (n_coureur)
			order by nom';
			
	$reqTous = 'select distinct Nom from
				(
					select distinct tdf_nation.nom as Nom from tdf_nation 
					join tdf_sponsor using (code_cio)
					union
					select distinct tdf_nation.nom as Nom from tdf_nation 
					join tdf_app_nation using (code_cio)
					join tdf_coureur using (n_coureur)
				)
				order by nom';
				
	$reqExis ='select distinct Nom from
				(
					select distinct tdf_nation.nom as Nom from tdf_nation 
					join tdf_sponsor using (code_cio)
					where annee_disparition is null
					union
					select distinct tdf_nation.nom as Nom from tdf_nation 
					join tdf_app_nation using (code_cio)
					join tdf_coureur using (n_coureur)
					where annee_disparition is null
				)
				order by nom';
	
	//selection du tableau à afficher :
	if (isset($_POST['continuer']) && $_POST['filtres']!='0') {
		switch ($_POST['filtres']) {
			case 'spon' : $nb = LireDonnees1($conn, $reqSpon, $tab); break;
			case 'cour' : $nb = LireDonnees1($conn, $reqCour, $tab); break;
			case 'tous' : $nb = LireDonnees1($conn, $reqTous, $tab); break;
			case 'exis' : $nb = LireDonnees1($conn, $reqExis, $tab); break;
		}
	}
	else {
		$nb = LireDonnees1($conn, $reqTous, $tab);
	}
	
	//fonction de traitement
	function affichage() {
		global $nb, $tab;

		$style = 'style= "border: 1px solid black;"';
		
		echo "<table>";
		foreach ($tab as $pays) {
			echo "<tr>";
			echo "<th $style>".$pays['NOM']."</th>";
			echo "</tr>";
		}
	}
	
	function select($ou) {
		if (isset($_POST['filtres']) && $ou == $_POST['filtres'])
			echo "selected";
	}
		
	include('../html/affichageNationParti.html');
?>