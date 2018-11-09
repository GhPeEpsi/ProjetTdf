<?php
	include ("../html/choixAnnee.html");
	//ce fichier sert à etre ajouté dans les autres fichiers php qui nécéssite une sélection
	//d'année de tdf :

	//Récupération de la liste des années
	function listeAnnee() {
		global $conn, $annee;
		$req = "select annee from tdf_annee order by annee";
		LireDonnees1($conn, $req, $tab);
		
		//première lignes :
		if (!empty($annee)) {echo '<option value="pasBon">Année</option>';}
		else {echo '<option value="pasBon" selected>Année</option>';}
		
		//insertion des années de la base :
		foreach ($tab as $sousTab) {
			$val = $sousTab['ANNEE'];
			if (!empty($annee) && $val == $annee)
				echo '<option value="'.$val.'" selected>'.$val.'</option>';
			else
				echo '<option value="'.$val.'">'.$val.'</option>';
		}
	}
?>