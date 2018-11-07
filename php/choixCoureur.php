<?php
	include ("pdo_oracle.php");
	include ("util_affichage.php");
	include ("../html/navBar.html");

	$db_username = 'ETU2_49';
	$db_password = 'ETU2_49';
	$db = "oci:dbname=spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr;charset=AL32UTF8";
	
	// $db_username = 'projet_php';
	// $db_password = 'projet_php';
	// $db = "oci:dbname=localhost:1521/xe;charset=AL32UTF8";
	
	$conn = OuvrirConnexion($db,$db_username,$db_password);
	
	
	//traitement :
	//recupération du type de tri :
	$orderBy = choixOrderBy();

	$req = '(
				select n_coureur, tdf_coureur.nom as Nom, prenom, annee_naissance, tdf_nation.nom as Pays, count(*) as Nb from tdf_coureur
				join tdf_app_nation using (n_coureur)
				join tdf_nation using (code_cio)
				join tdf_parti_coureur using (n_coureur)
				group by n_coureur, tdf_coureur.nom, prenom, annee_naissance, tdf_nation.nom
			)union(
				(
					select n_coureur, tdf_coureur.nom as Nom, prenom, annee_naissance, tdf_nation.nom as Pays, 0 as Nb from tdf_coureur
					join tdf_app_nation using (n_coureur)
					join tdf_nation using (code_cio)
				)minus(
					select n_coureur, tdf_coureur.nom as Nom, prenom, annee_naissance, tdf_nation.nom as Pays, 0 as Nb from tdf_coureur
					join tdf_app_nation using (n_coureur)
					join tdf_nation using (code_cio)
					join tdf_parti_coureur using (n_coureur)
					group by n_coureur, tdf_coureur.nom, prenom, annee_naissance, tdf_nation.nom
				)
			) '
		.$orderBy;
	$nbLignes = LireDonnees1($conn,$req,$tab);
		
	//function	
	function choixOrderBy() {
		if (isset($_GET['tri']))
			$tri = $_GET['tri'];
		else
			$tri = 'null';
		
		if (isset($_GET['sens']))
			$sens = $_GET['sens'];
		else
			$sens = 'null';
		
		$retour= "";
		
		switch($tri) {
			case 'n_coureur' : $retour = $retour . 'order by n_coureur';break;
			case 'nom' : $retour = $retour . 'order by nom';break;
			case 'prenom' : $retour = $retour . 'order by prenom';break;
			case 'naissance' : $retour = $retour . 'order by annee_naissance';break;
			case 'nation' : $retour = $retour . 'order by Pays';break;
			case 'nb' : $retour = $retour . 'order by Nb';break;
			default : $retour = $retour . 'order by n_coureur';
		}
		
		switch ($sens) {
			case 'asc' : $retour = $retour . ' asc';break;
			case 'desc' : $retour = $retour . ' desc';break;
			default : $retour = $retour . ' asc';
		}
		
		return $retour;
	}

	
	function affichageTableau() {
		global $tab;
		foreach($tab as $coureur) {
			echo '<tr>';
			foreach($coureur as $key => $colonne) {
				if ($key == 'N_COUREUR')
					echo '<th scope="row">'.$colonne.'</th>';
				else
					echo '<td>'.$colonne.'</td>';
			}
			echo '<th scope="col">
					  <a href="modificationCoureur.php?numCoureur='.$coureur["N_COUREUR"].'">Modifier</a>
				  </th>';
			echo '<th scope="col">
					  <a href="affichageCoureur.php?numCoureur='.$coureur["N_COUREUR"].'">Informations</a>
				  </th>';
			echo '<th scope="col">'
					  .pouvoirSupprimer($coureur).
				  '</th>';
		}
	}
	
	function pouvoirSupprimer($coureur) {
		if ($coureur['NB'] == 0)
			return '<a href="supprimerUnCoureur.php?numCoureur='.$coureur["N_COUREUR"].'">Supprimer</a>';
		else
			return 'Non Supprimable';
	}
	
	include("../html/choixCoureur.html");
?>