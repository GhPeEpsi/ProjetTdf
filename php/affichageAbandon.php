<?php
    include ("pdo_oracle.php");
    include ("util_affichage.php");
    include ("../html/navBar.html");

    /* Serveur UNICAEN */
    $login = 'ETU2_49';
    $mdp = 'ETU2_49';
    $db = "oci:dbname=spartacus.iutc3.unicaen.fr:1521/info.iutc3.unicaen.fr;charset=AL32UTF8";
    $db = fabriquerChaineConnexion();
    $conn = OuvrirConnexion($db,$login,$mdp);

    /* Localhost Noé */
    // $login = 'copie_tdf_copie';
    // $mdp = 'copie_tdf_copie';
    // $db = fabriquerChaineConnexion2();
    // $conn = OuvrirConnexion($db,$login,$mdp);

    //PROGRAMME PRINCIPAL :
    $annee;
    if (isset($_POST['verifier'])) {
        if (isset($_POST['annee']) && $_POST['annee'] != "pasBon") {
            $annee = $_POST['annee'];
        }
    }

    //REQUETE :
    $requete = 'SELECT n_epreuve, nom, prenom, libelle, aba.commentaire, typ.commentaire AS raison
                FROM tdf_abandon aba
                JOIN tdf_typeaban typ USING (c_typeaban)
                JOIN tdf_coureur cou USING (n_coureur)
                WHERE annee = :annee
                ORDER BY n_epreuve';
    $curseur = preparerRequete($conn,$requete);

    //affichage du tableau :
    function affichage() {
        global $conn, $annee, $curseur, $requete;
        
        //affichage du tableau quoi qu'il se passe :
        $style = "style=\"border: 1px solid black; margin :auto;\"";
        echo "<table $style>";
        echo "<tr $style>
              <th $style> N° Epreuve </th>
              <th $style> Coureur </th>
              <th $style> Libellé </th>
              <th $style> Commentaire </th>
              <th $style> Raison de l'abandon </th>
              </tr>";
        
        //affichage des données si une annee est entrée :
        if (isset($annee)) {
            //ajout de l'année aux requetes :
            ajouterParam($curseur,':annee',$annee);
            
            //boucle d'affichage :
            $nbLignes = LireDonneesPreparees($curseur,$tab);
            $j=0;
            foreach($tab as $ligne) {
                // echo "<PRE>";
                // print_r($ligne);
                // echo "</PRE>";
                $tableau = array();
                $tableau[] = $tab[$j];
                $j++;
                afficheLigneTableau($tableau, $style);
            }
        }
        else {
            echo "</table>";
            echo '<p style = "text-align : center">Pas encore d\'année selectionnée !</p>';
        }
    }

    function afficheLigneTableau($tab, $style) {
        
        if (!isset($tab[1])) {
            echo '<tr '.$style.'>
                  <th '.$style.'>'.$tab[0]['N_EPREUVE'].'</th>
                  <th '.$style.'>'.utf8_encode($tab[0]['NOM']). ' ' . utf8_encode($tab[0]['PRENOM']).'</th>
                  <th '.$style.'>'.utf8_encode($tab[0]['LIBELLE']).'</th>
                  <th '.$style.'>'.utf8_encode($tab[0]['COMMENTAIRE']).'</th>
                  <th '.$style.'>'.utf8_encode($tab[0]['RAISON']).'</th>
                  </tr>';
        }
        else {
            echo '<tr '.$style.'>
                  <th '.$style.'>'.$tab[0]['N_EPREUVE'].'</th>
                  <th '.$style.'>'.afficheNomPrenom($tab).'</th>
                  <th '.$style.'>'.$tab[0]['LIBELLE'].'</th>
                  <th '.$style.'>'.$tab[0]['COMMENTAIRE'].'</th>
                  <th '.$style.'>'.$tab[0]['RAISON'].'</th>
                  </tr>';
        }
    }

    function afficheNomPrenom($tab) {
        $retour = '';
        foreach ($tab as $ligne) {
            $retour = $retour . utf8_encode($ligne['NOM']). ' ' . utf8_encode($ligne['PRENOM']).'<br>';
        }
        return $retour;
    }

    //LE FICHIER HTML:
    include("../html/affichageAbandon.html");

?>