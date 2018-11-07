<?php
	
	// echo "<span> Nom Coureur: $nom </span>";
	// echo "<span> Prénom Coureur: $prenom </span>";
	// echo "<span> Année de naissance Coureur: $anneeNaisance </span>";
	// echo "<span> Nationalité Coureur: $nat </span>";
	// echo "<span>Depuis : $depuisQ </span>"

	// if (isset($_POST['nom'])) {
	// 	echo $nom;
	// }

	// if (isset($_POST['prenom'])) {
	// 	echo $prenom;
	// }

	// if (isset($_POST['dateN'])) {
	// 	echo $dateN;
	// }

	// if (isset($_POST['nationalite'])) {
	// 	echo $nat;
	// }

	// if (isset($_POST['depuisQ'])) {
	// 	echo $depuisQ;
	// }

	$textFinal1 = "";

	if (empty($_POST['Nom']) || empty($_POST['prenom']) || $_POST['nationalite'] == ''){
			echo "<script> alert('vous n\'avez pas tout rempli') </script>";
		}else{
				

				$textFinal1 = $textFinal1."Nom coureur: ". $nom;
				$textFinal1 = $textFinal1."<br>";
				$textFinal1 = $textFinal1."Prenom Coureur: ". $prenom;
				$textFinal1 = $textFinal1."<br>";
				$textFinal1 = $textFinal1."Nationalité : ". $nat;
				$textFinal1 = $textFinal1."<br>";

				if(!empty($_POST['dateN']) && empty($_POST['depuisQ'])){
					$verifInt = $_POST['dateN'];
					
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
							$annee_naissance = recupAnnee();
							$depuisQuand = $annee_naissance;	
						}

						if($depuisQuand > $annee_naissance){
							$textFinal1 = $textFinal1."<br> Vérifier que l'année entrée dans \"depuis Quand\" est inférieure à l'année de naissance";
						}else{

							$textFinal1 = $textFinal1."Date de naissance : ". $annee_naissance;
							$textFinal1 = $textFinal1."<br>";
							$textFinal1 = $textFinal1."Depuis : ". $depuisQuand;
							$textFinal1 = $textFinal1."<br>";
						}
					}
					if (empty($_POST['dateN']) && !empty($_POST['depuisQ'])) {
						$textFinal1 = $textFinal1."<br> Vous devez entrer une année de naissance si vous remplissez depuisQ";
					}
				}
			}	

?>