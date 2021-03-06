<?php
mb_internal_encoding("UTF-8");

$regex = "#^[a-zA-ZÀÂÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïñðòóôõöùúûüýÿæÆœŒø '-]{1,}$#";

function supprimeAccents($str, $isPrenom, $encoding='UTF-8') {
    // transformer les caractères accentués en entités HTML
    $str = htmlentities($str, ENT_NOQUOTES, $encoding);

    // remplacer les entités HTML pour avoir juste le premier caractères non accentués
    // Exemple : "&ecute;" => "e", "&Ecute;" => "E", "à" => "a" ...
    //remplace seulement le premier caractère si la chaine est un prénom
    if ($isPrenom) {
        $str = preg_replace('#^&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#^&([A-za-z]{2})(?:lig);#', '\1', $str);
    } else {
        $str = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    }

    $str = preg_replace('#Ŭ|ŭ#', 'u', $str);

    return $str;
}

function supprimeCaracteresSpeciaux($str, $encoding='UTF-8') {
   
    // Remplacer les ligatures tel que : , Æ ...
    // Exemple "œ" => "oe"
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);

    $str = preg_replace('#&([A-za-z])(?:slash|tilde);#', '\1', $str);

    // Supprimer les tirets en début et fin de chaine
    while (preg_match('#^-|-$#', $str)) {
        $str = preg_replace('#^-|-$#', '', $str);
    }
    
    // Supprime les espaces après et/ou avant des tirets
    $str = preg_replace('# - |- | -#', '-', $str);
    // Supprime les espaces après et/ou avant des apostrophes
    $str = preg_replace('# \' |\' #', '\'', $str);

    return $str;
}


function testNom($nom, $regex) {
    if (preg_match($regex, $nom) && !(preg_match("#---|''|^'$|^-$#", $nom))){
        //retire caractères interdits et convertit en majuscules
        $nom = strtoupper(supprimeCaracteresSpeciaux(supprimeAccents($nom, FALSE)));

        $nom = utf8_encode($nom);

        if (iconv_strlen($nom, 'UTF-8') > 30) {
            echo "<script> alert('Le nom saisi ne doit pas dépasser 30 caractères !')</script>";
            return NULL;
        }

        $array = preg_split("#--#", $nom);
        if (count($array) > 2) {
            echo "<script> alert('Vous ne pouvez saisir de doubles-tirets qu\'une fois !')</script>";
            return NULL;
        }

        return $nom;
    } else { 
        echo "<script> alert('Le nom saisi n\'est pas valide !')</script>";

        return NULL;  
    }
}

function testPrenom($prenom, $regex) {
    if (preg_match($regex, $prenom) && !(preg_match("#--|''|^'$|^-$#", $prenom))) {
        
        $prenom = mb_strtolower($prenom, "UTF-8");
        $array = preg_split("#('|-| )#", $prenom, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        
        //retire l'eventuel accent sur le premier caractère et le remplace par une majuscule.
        foreach ($array as $key => $value) {
            
            $array[$key] = $value = ucfirst(supprimeAccents($value, TRUE));
        }

        $prenom = implode($array);
        $prenom = supprimeCaracteresSpeciaux($prenom);

        $prenom = html_entity_decode($prenom, ENT_NOQUOTES, "UTF-8");
        
        //verifie si le prénom saisi dépasse 30 caractères
        if (iconv_strlen($prenom, 'UTF-8') > 30) {
            echo "<script> alert('Le prénom saisi ne doit pas dépasser 30 caractères !')</script>";
            return NULL;
        }

        return $prenom;
    } else {
         
        echo "<script> alert('Le prenom saisi n\'est pas valide !')</script>";
        
        return NULL;
    }
}

function testDate($date) {
    $regexDate = "#^[0-9]{4}$#";
    if (preg_match($regexDate, $date) && intval($date) > 1900 && intval($date) <= intval(date("Y"))) {
        return $date;
    } else {
        echo "<script>alert('L\'année saisie n\'est pas valide')</script>";
        return NULL;
    }
}

function testNomSponsor($sponsor) {
    $regexSp = "#^.{2,30}$#";
    if (preg_match($regexSp, $sponsor)){
        //retire caractères interdits et convertit en majuscules
        $sponsor = strtoupper(supprimeAccents($sponsor, FALSE));


        if (iconv_strlen($sponsor, 'UTF-8') > 30) {
            echo "<script> alert('Le nom de sponsor saisi ne doit pas dépasser 30 caractères !')</script>";
            return NULL;
        }

        return $sponsor;
    } else {
        
        echo "<script> alert('Le nom de sponsor saisi contient des caractères interdit !')</script>";

        return NULL;  
    }
}

function testNomAbrege($sponsor) {
    $regexSp = "#^.{0,}$#";
    if (preg_match($regexSp, $sponsor)){
        //retire caractères interdits et convertit en majuscules
        $sponsor = strtoupper(supprimeAccents($sponsor, FALSE));


        if (iconv_strlen($sponsor, 'UTF-8') > 3) {
            echo "<script> alert('Le nom de sponsor saisi ne doit pas dépasser 3 caractères !')</script>";
            return NULL;
        }

        return $sponsor;
    } else {
        
        echo "<script> alert('Le nom de sponsor saisi contient des caractères interdit !')</script>";

        return NULL;  
    }
}

?>