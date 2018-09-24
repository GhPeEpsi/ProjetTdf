--Afficher tous les coureurs :
select * from tdf_coureur;
select * from tdf_coureur order by N_coureur;
select * from tdf_coureur order by Nom;
select * from tdf_coureur order by prenom;
select * from tdf_coureur order by annee_prem;

--Trouver un coureur en particulier :
SELECT * from tdf_coureur where Nom like 'A%'; -- nom changeable par prenom, n_coureur, annee_prem
SELECT * from tdf_coureur where Nom like '%A%';
SELECT * from tdf_coureur where Nom like '%A';

--vérification si un coureur à participé ou non au TDF :
select count(*) from tdf_coureur co
join tdf_parti_coureur using (n_coureur)
where co.nom like 'GRUT';

--insertion dans la base d'un coureur comme il faut :
INSERT INTO tdf_coureur (n_coureur, nom, prenom, annee_naissance, annee_prem)
VALUES (
(
    select max(n_coureur) from vt_coureur
), 'GROUT', 'Je-s-appelle', 2010, 2018);
    
    --Sous requètes utile à l'ajout d'un coureur :
    select max(n_coureur) from tdf_coureur;
    
--insertion tdf_app_nation
INSERT INTO tdf_app_nation(;
    

select * from tdf_app_nation;