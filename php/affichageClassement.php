<?php
    include ("pdo_oracle.php");
	include ("util_affichage.php");
	include ("../html/navBar.html");

	/*Serveur UNICAEN*/
	$login = 'ETU2_49';
	$mdp = 'ETU2_49';
	$db = "oci:dbname=spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr;charset=AL32UTF8";
	// $db = fabriquerChaineConnexion();
	$conn = OuvrirConnexion($db,$login,$mdp);

    //PROGRAMME PRINCIPAL :
    $orderBy = choixOrderBy();

	$annee;
	if (isset($_POST['verifier'])) {
		if (isset($_POST['annee']) && $_POST['annee'] != "pasBon") {
			$annee = $_POST['annee'];
		}
	}

	if (isset($_GET['annee'])) {
		$annee = $_GET['annee'];
	}

	//REQUETE :
	$reqLigne = 'select distinct rang, code_pays, nom, prenom, temps  from tdf_classements_generaux join
tdf_parti_coureur 
using(n_coureur) where tdf_classements_generaux.annee = :annee and valide != \'R\''.$orderBy;

	$curLigne = preparerRequete($conn,$reqLigne);
    

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
			case 'rang' : $retour = $retour . 'order by rang';break;
			case 'nation' : $retour = $retour . 'order by CODE_PAYS';break;
			case 'coureur' : $retour = $retour . 'order by nom';break;
			case 'temps' : $retour = $retour . 'order by temps';break;
			default : $retour = $retour . 'order by rang';
		}
		
		switch ($sens) {
			case 'asc' : $retour = $retour . ' asc';break;
			case 'desc' : $retour = $retour . ' desc';break;
			default : $retour = $retour . ' asc';
		}
		
		return $retour;
	}


    //affichage du tableau :
	function affichageClassement() {
		global $conn, $annee, $curLigne;
		
		//affichage des données si une annee est entrée :
		if (isset($annee)) {
			//ajout de l'année aux requetes :
			ajouterParam($curLigne,':annee',$annee);
			
			//résultat :
			$nbLignes = LireDonneesPreparees($curLigne, $tabRes);

			$style = "style=\"border: 1px solid black; margin: auto;\"";
			echo "<table $style>";
			echo '<tr $style>
				<th $style>
					<a href="affichageClassement.php?tri=rang&sens=asc&annee='.$annee.'">#</a>
					<a href="affichageClassement.php?tri=rang&sens=asc&annee='.$annee.'">↑</a>
					<a href="affichageClassement.php?tri=rang&sens=desc&annee='.$annee.'">↓</a>
				</th>
				<th $style>
					<a href="affichageClassement.php?tri=nation&sens=asc&annee='.$annee.'">Nation</a>
					<a href="affichageClassement.php?tri=nation&sens=asc&annee='.$annee.'">↑</a>
					<a href="affichageClassement.php?tri=nation&sens=desc&annee='.$annee.'">↓</a>
					</th>
				<th $style>
					<a href="affichageClassement.php?tri=coureur&sens=asc&annee='.$annee.'">Coureur</a>
					<a href="affichageClassement.php?tri=coureur&sens=asc&annee='.$annee.'">↑</a>
					<a href="affichageClassement.php?tri=coureur&sens=desc&annee='.$annee.'">↓</a>
				</th>
				<th $style>
					<a href="affichageClassement.php?tri=temps&sens=asc&annee='.$annee.'">Temps</a>
					<a href="affichageClassement.php?tri=temps&sens=asc&annee='.$annee.'">↑</a>
					<a href="affichageClassement.php?tri=temps&sens=desc&annee='.$annee.'">↓</a>
				</th>
			</tr>';

			//boucle d'affichage :
			foreach ($tabRes as $ligne) {
				//echo "salut";
				afficheLigneTableau($ligne, $style);
			}
			echo "</table>";
		}
		else {
			echo "</table>";
			echo '<p style ="text-align:center">Pas encore d\'année selectionnée !</p>';
		}
	}

	function afficheLigneTableau($tab, $style) {
		echo '<tr '.$style.'>
			<th '.$style.'>'.$tab['RANG'].'</th>
			<th '.$style.'>'.$tab['CODE_PAYS'].'</th>
			<th '.$style.'>'.$tab['NOM']. ' ' .$tab['PRENOM'].'</th>
			<th '.$style.'>'.getTemps($tab['TEMPS']).'</th>
			</tr>';
	}

	function getTemps($temps) {
		$heures = floor(intval($temps) / 3600);
		$minutes =  floor((intval($temps) % 3600) / 60);
		$secondes = floor((intval($temps) % 3600) % 60);

		return $heures."h".$minutes."'".$secondes."''";
	}

	function saveAnnee(){
		global $annee;
		echo '<input type ="hidden" name="saveAnnee" value ="'.$annee.'">';
	}

	//LE FICHIER HTML:
	include("../html/affichageClassement.html");

?>