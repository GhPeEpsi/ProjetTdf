<?php

	//Variable qui incrémentera tous les messages à afficher au fur et à mesure et qui va être affichée à la fin par la fonction "afficherTextFinal".
	$textFinal1 = "";

/* --------------------------------------------------------------------------------------------------------------------------------------------------------- */
/* ------------------------------------------- 1). verfication du bon remplissage des champs obligatoire : ------------------------------------------------- */
/*---------------------------------------------------------------------------------------------------------------------------------------------------------- */
	if (empty($_POST['Nom']) || empty($_POST['prenom']) || $_POST['nationalite'] == ''){
			echo "<script> alert('vous n\'avez pas tout rempli') </script>";
		}else{
				
				//On remplit la variable textFinal1 par les differents messages à afficher
				$textFinal1 = $textFinal1."Nom coureur: ". $nom;
				$textFinal1 = $textFinal1."<br>";
				$textFinal1 = $textFinal1."Prenom Coureur: ". $prenom;
				$textFinal1 = $textFinal1."<br>";
				$textFinal1 = $textFinal1."Nationalité : ". $nat;
				$textFinal1 = $textFinal1."<br>";

/* --------------------------------------------------------------------------------------------------------------------------------------------------------- */
/* 2) a) vérification de la validité d'une année entrée. Si on remplit le champs "année de naissance" mais que l'on ne remplit pas le champs "Depuis quand". */
/*---------------------------------------------------------------------------------------------------------------------------------------------------------- */

				if(!empty($_POST['dateN']) && empty($_POST['depuisQ'])){
					$verifInt = $_POST['dateN'];
					
					//Si l'annee entrée n'est pas un entier et que cet entier n'est pas entre 1900 et l'année actuelle alors on envoie un message d'erreur.
					if(!ctype_digit($verifInt)|| $verifInt < 1900 || $verifInt > date('Y')){
						$textFinal1 = $textFinal1."<br> Vous n'avez pas entré une date valide.";
					}
					else{
						$annee_naissance = recupAnnee();
						$depuisQuand = $annee_naissance;
						
						$textFinal1 = $textFinal1."Date de naissance : ". $annee_naissance;
						$textFinal1 = $textFinal1."<br>";
						$textFinal1 = $textFinal1."Depuis : ". $depuisQuand;
						$textFinal1 = $textFinal1."<br>";
					}
				}else{
					if(!empty($_POST['dateN'])&& (!empty($_POST['depuisQ']))){
						$verifInt = $_POST['dateN'];
						if(!ctype_digit($verifInt)|| $verifInt < 1900 || $verifInt > date('Y')){
							$textFinal1 = $textFinal1."<br> Vous n'avez pas entré une date valide";
						}else{
							//On récupere cette année
							$annee_naissance = recupAnnee();
							//comme le champs depuisQuand etait vide, on le remplis automatiquement par l'année entrée dans Année de naissance
							$depuisQuand = $annee_naissance;	
						}
						//Si jamais l'année entrée dans "Depuis Quand" est supérieure à l'année entrée dans "Année de naissance", alors on envoie un message d'erreur. Sinon, on affiche le contenu des variables .
						if($depuisQuand > $annee_naissance){
							$textFinal1 = $textFinal1."<br> Vérifier que l'année entrée dans \"depuis Quand\" est inférieure à l'année de naissance";
						}else{

							$textFinal1 = $textFinal1."Date de naissance : ". $annee_naissance;
							$textFinal1 = $textFinal1."<br>";
							$textFinal1 = $textFinal1."Depuis : ". $depuisQuand;
							$textFinal1 = $textFinal1."<br>";
						}
					}

/* --------------------------------------------------------------------------------------------------------------------------------------------------------- */
/*------------------ 2) c) Si on ne remplit pas "Année de naissance" mais que l'on remplit "Depuis Quand", on renvoie un message d'erreur.------------------ */
/*---------------------------------------------------------------------------------------------------------------------------------------------------------- */
					if (empty($_POST['dateN']) && !empty($_POST['depuisQ'])) {
						$textFinal1 = $textFinal1."<br> Vous devez entrer une année de naissance si vous remplissez depuis Quand";
					}
				}
			}	

?>