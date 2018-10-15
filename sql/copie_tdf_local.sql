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

--TDF_participation :
SELECT * FROM tdf_parti_coureur;

--vérification si un coureur à participé ou non au TDF :
select count(*) from tdf_coureur co
join tdf_parti_coureur using (n_coureur)
where co.nom like 'GRUT'
and co.prenom like 'Abel'
group by co.nom, co.prenom; -- doit dabors creer une participation avant de compter

--insertion dans la base d'un coureur comme il faut :
INSERT INTO tdf_coureur (n_coureur, nom, prenom, annee_naissance, annee_prem)
VALUES (
(
    select max(n_coureur) from vt_coureur
) + 1, 'GROUT', 'Abel', 2010, 2018);
    
    --Sous requètes utile à l'ajout d'un coureur :
    select max(n_coureur) from tdf_coureur;
    --apartenance à un pays :
    INSERT INTO tdf_app_nation (n_coureur, code_cio, annee_debut)
    Values (
    
    );


--création d'une participation au tdf
    --une participation ne peux pas dater du passé
INSERT INTO tdf_parti_coureur(annee, n_coureur, n_equipe, n_sponsor, n_dossard)
VALUES (
(
    select max(ANNEE) from tdf_annee
), 2, 1, 1, 1);
    --Recherche de l'année max
    select max(ANNEE) from tdf_annee;
    --Recherche des bonnes values :
    select n_coureur from tdf_coureur 
    join tdf_app_nation using (n_coureur)
    where nom = 'GROUT'
    and prenom = 'Abel'
    and code_cio = 'FR';
    
--Création d'une nouvelle année :
INSERT INTO tdf_annee(annee) VALUES 
(
    select to_char(sysdate,'yyyy') from dual
);

--Requéte de recherche de pays existants pour une date de naissance :
select code_cio, nom from tdf_nation
where annee_creation <1992
and annee_disparition >1992
UNION
select code_cio, nom from tdf_nation
where annee_creation is null
and annee_disparition is null;
    --Recherche pour la requète :
    select code_cio, nom, annee_creation, annee_disparition from tdf_nation;
    select code_cio, nom, annee_creation, annee_disparition from tdf_nation where code_cio = 'URS';
    
    select * from tdf_app_nation;
    
--insertion tdf_app_nation
INSERT INTO tdf_app_nation(;

--recherche de la date :
select To_char(sysdate,'yyyy') from dual;
select * from tdf_ANNEE;


select * from tdf_nation where nom = 'RUSSIE'; 
select * from tdf_coureur order by nom;


--Requête pour inserer la nation d'un coureur.
Insert into tdf_app_nation(n_coureur, code_cio,annee_debut) values ((select max(n_coureur) from tdf_coureur),'FRA', '1969');

    --aides
    select * from tdf_app_nation;
    select * from tdf_nation;
    select count(*) from tdf_coureur;
    
--Recherche sur un coureur :
    --n_coureur, nom, prenom, nation, ... :
    select n_coureur, co.nom, prenom, annee_naissance, annee_prem, na.nom
    from tdf_coureur co
    join tdf_app_nation using (n_coureur)
    join tdf_nation na using (code_cio)
    order by n_coureur;
    --nb de participation :
    select count(*) from tdf_parti_coureur
    join tdf_coureur using (n_coureur)
    where nom = 'JOACHIM'
    and prenom = 'Benoit';
    --équipe :
    

