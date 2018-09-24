<?php
//Connexions
//$conn = OuvrirConnexion('ETU2_33', 'ETU2_33','info');
//$conn = @OuvrirConnexion('ETU2_33', 'ETU2_33','spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr');


function OuvrirConnexionOCI($session,$mdp,$instance) {
	//$conn = @oci_connect($session,$mdp,$instance);
	//$conn = oci_connect($session,$mdp,$instance,"WE8ISO8859P15");
	$conn = oci_connect($session,$mdp,$instance,"AL32UTF8");
	if(!$conn) {  
		$e = oci_error();
		exit;
	}
	return $conn;
}


function OuvrirConnexionOCI2($session,$mdp,$instance) {
	$conn = @oci_connect($session,$mdp,$instance);
	if(!$conn) {  // retourne une erreur s'il n'y a pas de connexion
		$e = oci_error();
		echo "<br>Votre nom d'utilisateur ou votre mot de passe est &eacute;ronn&eacute;e, veuillez vous reconnecter...<br>";
		echo "<form action = '12_2a.htm' method='post' enctype='application/x-www-form-urlencoded'>
				<input type='submit' value='Retour'>
		  </form>";
		exit;
	}
	return $conn;
}


function PreparerRequeteOCI($conn,$req) {
	$cur = oci_parse($conn,$req);
	if(!$cur) {  
		$e = oci_error($conn);  
		print htmlentities($e['message']);  
		exit;
	}
	return $cur;
}


function ExecuterRequeteOCI($cur) {
	$r = oci_execute($cur,OCI_DEFAULT);
	if(!$r) {  
		$e = oci_error($r);  
		echo htmlentities($e['message']);  
		exit;
	}
	return $r;
}


function ajouterParamOCI($cur,$param,&$variable,$taille=0) { // fonctionne avec preparerRequeteOCI
	$res = oci_bind_by_name($cur,$param,$variable,$taille);
	return $res;
}


function ValiderTransacOCI($conn) {
	$res = oci_commit($conn);
	return $res;
}


function FermerConnexionOCI($conn) {
	$res = oci_close($conn);
	return $res;
}


function LireDonneesOCI1($cur,&$tab) {
	$nbLignes = oci_fetch_all($cur, $tab,0,-1,OCI_FETCHSTATEMENT_BY_ROW|OCI_ASSOC); //OCI_FETCHSTATEMENT_BY_ROW, OCI_ASSOC, OCI_NUM
	//$nbLignes = oci_fetch_all($cur, $tab,0,-1,OCI_FETCHSTATEMENT_BY_COLUMN|OCI_ASSOC); //OCI_FETCHSTATEMENT_BY_ROW, OCI_ASSOC, OCI_NUM
	//$nbLignes = oci_fetch_all($cur, $tab,0,-1,OCI_FETCHSTATEMENT_BY_ROW|OCI_NUM); //OCI_FETCHSTATEMENT_BY_ROW, OCI_ASSOC, OCI_NUM
	//$nbLignes = oci_fetch_all($cur, $tab,0,-1,OCI_FETCHSTATEMENT_BY_COLUMN|OCI_NUM); //OCI_FETCHSTATEMENT_BY_ROW, OCI_ASSOC, OCI_NUM
	return $nbLignes;
}


function LireDonneesOCI2($cur,&$tab) {
	$nbLignes = 0;
	$i=0;
	while($row = oci_fetch_array($cur,OCI_BOTH)) {    
		$tab[$nbLignes][$i] = $row[0];
		$tab[$nbLignes][$i+1] = $row[1];
		$tab[$nbLignes][$i+2] = $row[2];
		$nbLignes++;
	}
	return $nbLignes;
}


function LireDonneesOCI3($cur,&$tab) {
	$nbLignes = 0;
	$i=0;
	while($row = oci_fetch($cur)) {    
		$tab[$nbLignes][$i] = oci_result($cur,'VAL'); // respecter la casse
		$tab[$nbLignes][$i+1] = oci_result($cur,'TYPE');
		$tab[$nbLignes][$i+2] = oci_result($cur,'COULEUR');
		$nbLignes++;
	}
	return $nbLignes;
}
?>