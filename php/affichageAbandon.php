<?php
    $req = 'SELECT annee, n_epreuve, n_coureur, libelle, ab.commentaire, ta.commentaire as raison
            FROM tdf_abandon ab JOIN tdf_typeaban ta USING (c_typeaban)
            WHERE annee = 2001 ORDER BY annee';
?>