<?php
/*  CONNEXION_PDO  -  CONNEXION_PDO  -  CONNEXION_PDO  -  CONNEXION_PDO  -  CONNEXION_PDO  -  CONNEXION_PDO
	CONNEXION_PDO  -  CONNEXION_PDO  -  CONNEXION_PDO  -  CONNEXION_PDO  -  CONNEXION_PDO  -  CONNEXION_PDO
	CONNEXION_PDO  -  CONNEXION_PDO  -  CONNEXION_PDO  -  CONNEXION_PDO  -  CONNEXION_PDO  -  CONNEXION_PDO
	CONNEXION_PDO  -  CONNEXION_PDO  -  CONNEXION_PDO  -  CONNEXION_PDO  -  CONNEXION_PDO  -  CONNEXION_PDO  */

/*	$db_username = "ETU2_49";	// Base UniCaen de Bastien
	$db_password = "ETU2_49";	// Base UniCaen de Bastien
	$db = "oci:dbname=info;charset=AL32UTF8"; // fonctionne si tnsname.ora est complet (base UTF8)
	$db = "oci:dbname=info;charset=WE8ISO8859P15"; // fonctionne si tnsname.ora est complet
	
	$db = fabriquerChaineConnex();
	$conn = Connecter($db,$db_username,$db_password);	*/



// Permet de se connecter à la base de données avec une instance PDO
function OuvrirConnexion($db,$db_username,$db_password) {
	try { // On teste si les paramètres de connexion sont valides
		$conn = new PDO($db,$db_username,$db_password);
		$res = true;
	} catch (PDOException $erreur) { // Sinon on affiche un message d'erreur
		echo $erreur->getMessage();
	}
	return $conn; 
}


// Permet de mettre à jour les données dans la base
function majDonnees($conn,$sql) {
	$stmt = $conn->exec($sql);
	return $stmt;
}


// Prépare un requête donnée et retourne l'objet
function preparerRequete($conn,$sql)
{
	$cur = $conn->prepare($sql);
	return $cur;
}


//
function ExecuterRequete($conn,$req) {
	$cur = oci_parse($conn,$req);
	if (!$cur) {  
		$e = oci_error($conn);  
		print htmlentities($e['message']);  
		exit;
	}
	$r = oci_execute($cur, OCI_DEFAULT);
	if (!$r) {  
		$e = oci_error($conn);  
		echo htmlentities($e['message']);  
		exit;
	}
	return $cur;
}


// 
function ajouterParam($cur,$param,$contenu,$type='texte',$taille=0) {
	// Sur Oracle, on peut tout passer sans préciser le type. Sur MySQL ???
	//	if ($type == 'nombre')
	//		$cur->bindParam($param, $contenu, PDO::PARAM_INT);
	//	else
	//		$cur->bindParam($param, $contenu, PDO::PARAM_STR, $taille);
	$cur->bindParam($param,$contenu);
	return $cur;
}


// Permet de mettre à jour les données préparées (variable)
function majDonneesPreparees($cur) {
	$res = $cur->execute();
	return $res;
}


// Permet de mettre à jour les données préparées (tableau)
function majDonneesPrepareesTab($cur,$tab) {
	$res = $cur->execute($tab);
	return $res;
}



function LireDonnees1($conn,$sql,&$tab) {
	$i=0;
	foreach($conn->query($sql,PDO::FETCH_ASSOC) as $ligne) {
		$tab[$i++] = $ligne;
	}
	$nbLignes = $i;
	return $nbLignes;
}



function LireDonnees2($conn,$sql,&$tab) {
	$i=0;
	$cur = $conn->query($sql);
	while($ligne = $cur->fetch(PDO::FETCH_ASSOC)) {
		$tab[$i++] = $ligne;
	}
	$nbLignes = $i;
	return $nbLignes;
}



function LireDonnees3($conn,$sql,&$tab) {
	$cur = $conn->query($sql);
	$tab = $cur->fetchall(PDO::FETCH_ASSOC);
	return count($tab);
}



function LireDonnees4($cur,&$tab) {
	$nbLignes = oci_fetch_all($cur, $tab,0,-1,OCI_ASSOC); //OCI_FETCHSTATEMENT_BY_ROW, OCI_ASSOC, OCI_NUM
	return $nbLignes;
}



function LireDonneesPreparees($cur,&$tab) {
  $res = $cur->execute();
  $tab = $cur->fetchall(PDO::FETCH_ASSOC);
  return count($tab);
}



function fabriquerChaineConnexion() {
	$hote = 'spartacus.iutc3.unicaen.fr';
	$port = '1521'; // port par défaut
	$service = 'info.iutc3.unicaen.fr';

	$db =
	"oci:dbname=(DESCRIPTION =
	(ADDRESS_LIST =
		(ADDRESS =
			(PROTOCOL = TCP)
			(Host = ".$hote .")
			(Port = ".$port."))
	)
	(CONNECT_DATA =
		(SERVICE_NAME = ".$service.")
	)
	)";
	return $db;
}



function fabriquerChaineConnexion2() {
	$hote = 'localhost';
	$port = '1521'; // port par défaut
	$service = 'xe';

	$db =
	"oci:dbname=(DESCRIPTION =
	(ADDRESS_LIST =
		(ADDRESS =
			(PROTOCOL = TCP)
			(Host = ".$hote .")
			(Port = ".$port."))
	)
	(CONNECT_DATA =
		(SERVICE_NAME = ".$service.")
	)
	)";
	return $db;
}
?>
