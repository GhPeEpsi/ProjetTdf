<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<title>Modificatio d'un coureur</title>
	</head>
	<body>
		<?php	/*
		include ("pdo_oracle.php");
		include ("util_affichage.php");
	
		$login = 'copie_tdf_copie';
		$mdp = 'copie_tdf_copie';
		$db = 'oci:dbname=localhost:1521/xe';
		
		$conn = OuvrirConnexion($db,$login,$mdp);
		$req = 'SELECT * FROM vt_coureur order by nom';
		$nbLignes = LireDonnees1($conn,$req,$tab);
		
		if (!empty($_POST)) {
			if (isset($_POST['coureur'])) {
				$cour = $_POST['coureur'];
				echo ("Coureur $cour sélectionné");
			}
		} else {
			include ("../html/modificationCoureur.html");
		}
		?>
		<?php
		$erreur = true;
		if ( !empty($_POST ))	
		{
			echo"<pre>";
			print_r($_POST);
			echo "</PRE>";
			echo "décodage sur le serveur <br />";
			  
			$erreur = false;
			if (!empty($_POST['nom']) && $_POST['nom'] != "nom ?") 
			$nom = $_POST['nom'];  
			else 
			{
				echo "le nom est vide <br />";
				$erreur = true;
			}
			if (!empty($_POST['prenom']) && $_POST['prenom'] != "") 
				$prenom = $_POST['prenom'] ; 
			else 
			{
				echo "le prénom est vide <br />";
				$erreur = true;
			}
			if (!empty($_POST['courriel']) && $_POST['courriel'] != "courriel ?") 
				$courriel = $_POST['courriel'] ; 
			else 
			{
				echo "le courriel est vide <br />";
				$erreur = true;
			}
			if (!empty($_POST['gouts']) && $_POST['gouts'] != "quoi d'autre ? ?") 
				$gouts = $_POST['gouts'] ; 
			else 
			{
				echo "les autres gouts ne sont pas remplis <br />";
				$erreur = true;
			}
			if (!empty($_POST['code'])) 
				$code = $_POST['code'] ; 
			else 
			{
				echo "le mot de passe est vide <br />";
				$erreur = true;
			}
			if (isset($_POST['civilite']) ) 
				$civ = $_POST['civilite'];  
			else 
			{
				echo "la civilité n'a pas été cochée <br />";
				$erreur = true;
			}
			if (isset($_POST['pays']) ) 
				$pays = $_POST['pays'];  
			else 
			{
				echo "le pays n'a pas été sélectionné <br />";
				$erreur = true;
			}
			if (isset($_POST['preference']) ) 
				$pref = $_POST['preference'];  
			else 
			{
				echo "aucune case n'a pas été cochée <br />";
				$erreur = true;
			}
			
			
			if (!empty($_POST['discret']) ) 	
				$val = $_POST['discret'];
					
			if ($erreur == false)
			{
				echo "NOM : $nom <br />";
				echo "PRENOM : $prenom <br />";
				echo "CIVILITE : $civ <br />";
				echo "PAYS : $pays <br />";
				echo "Gouts : $gouts <br />";
				$gouts = stripslashes($gouts);
				echo "Gouts : $gouts<br>";
				echo "caché : $val<br>";
				foreach ($pref as $val)
					echo $val.'<br/>';

				$fic1 = $_FILES['fichier']['name'];
				$fic2 = $_FILES['fichier']['type'];
				$fic3 = $_FILES['fichier']['size'];
				$fic4 = $_FILES['fichier']['tmp_name'];
				$fic5 = $_FILES['fichier']['error'];

				echo "fic1 : $fic1<br>"; 
				echo "fic2 : $fic2<br>"; 
				echo "fic3 : $fic3<br>"; 
				echo "fic4 : $fic4<br>"; 
				echo "fic5A : $fic5<br>"; 

				$result=move_uploaded_file($fic4,$fic1);
				if($result==TRUE)
				  echo "<hr /><big>Le  transfert est réalisé !</big>";
				else
				  echo "<hr /> Erreur de transfert n°",$fic5;

			}
		}
		
		if ($erreur == true) {
			include ("util_chap9.php");
			include ("modificationCoureur.html")	 ;
		}	*/
		?>	
	</body>
</html>