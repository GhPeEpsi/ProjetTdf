--Afficher tous les coureurs :
select * from vt_coureur;
select * from vt_coureur order by N_coureur;
select * from vt_coureur order by Nom;
select * from vt_coureur order by prenom;
select * from vt_coureur order by annee_prem;

--Trouver un coureur en particulier :
SELECT * from vt_coureur where Nom like 'A%'; -- nom changeable par prenom, n_coureur, annee_prem
SELECT * from vt_coureur where Nom like '%A%';
SELECT * from vt_coureur where Nom like '%A';

--v�rification si un coureur � particip� ou nom au TDF :
select * from vt_coureur


select * from tdf_categorie_epreuve;