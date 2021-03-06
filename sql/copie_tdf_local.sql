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

--v�rification si un coureur � particip� ou non au TDF :
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
    
    --Sous requ�tes utile � l'ajout d'un coureur :
    select count(*) as nb from tdf_coureur 
    join tdf_app_nation using (n_coureur)
    where nom = 'GROUT'
    and prenom = 'Abel'
    and code_cio = 'FRA'
    group by nom, prenom, code_cio;
    --apartenance � un pays :
    INSERT INTO tdf_app_nation (n_coureur, code_cio, annee_debut)
    Values (
        (
        select max(n_coureur) from tdf_coureur
        ), 
        'FRA', 2010
    );


--cr�ation d'une participation au tdf
    --une participation ne peux pas dater du pass�
INSERT INTO tdf_parti_coureur(annee, n_coureur, n_equipe, n_sponsor, n_dossard)
VALUES (
(
    select max(ANNEE) from tdf_annee
), 2, 1, 1, 1);
    --Recherche de l'ann�e max
    select max(ANNEE) from tdf_annee;
    --Recherche des bonnes values :
    select n_coureur from tdf_coureur 
    join tdf_app_nation using (n_coureur)
    where nom = 'GROUT'
    and prenom = 'Abel'
    and code_cio = 'FR';
    
--Cr�ation d'une nouvelle ann�e :
INSERT INTO tdf_annee(annee) VALUES 
(
    select to_char(sysdate,'yyyy') from dual
);

--Requ�te de recherche de pays existants pour une date de naissance :
select code_cio, nom from tdf_nation
where annee_creation <1992
and annee_disparition >1992
UNION
select code_cio, nom from tdf_nation
where annee_creation is null
and annee_disparition is null;
    --Recherche pour la requ�te :
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


--Requ�te pour inserer la nation d'un coureur.
Insert into tdf_app_nation(n_coureur, code_cio,annee_debut) values ((select max(n_coureur) from tdf_coureur),'FRA', '1969');

    --aides
    select * from tdf_app_nation;
    select * from tdf_nation;
    select count(*) from tdf_coureur;
    
--Recherche sur un coureur :
    select n_coureur as "Numero de coureur", co.nom as "Nom", prenom as "Prenom", annee_naissance as "Annee de naissance", annee_prem as "Annee de premi�re", na.nom as "Nation"
    from tdf_coureur co
    join tdf_app_nation using (n_coureur)
    join tdf_nation na using (code_cio)
    where co.nom = 'JOACHIM'
    and prenom = 'Benoit';
    --Ann�e o� il a particip� :
    select annee from tdf_parti_coureur
    join tdf_coureur using (n_coureur)
    where nom = 'JOACHIM'
    and prenom = 'Benoit';
   
    --place � chaque tour :
    select * from tdf_temps;
    
    --temps total d'un coureur
    select sum(total_seconde) as "Temps Total" from tdf_coureur co
    join tdf_parti_coureur using (n_coureur)
    join tdf_temps using (n_coureur, annee)
    where nom = 'JOACHIM'
    and prenom = 'Benoit'
    and annee = 2000
    group by annee, nom, prenom;
   
    --IL FAUT CALCULER SON TEMPS TOTAL SUR LE TOUR ET REGARDER PAR RAPPORT AU TEMPS TOTAL DES AUTRES // IL FAUT ENLEVER CEUX QUI ONT ABANDONN�
    select count(tmp) as nb from
    (
        (
            select annee, nom, prenom, sum(total_seconde) as tmp from tdf_coureur co
            join tdf_parti_coureur using (n_coureur)
            join tdf_temps using (n_coureur, annee)
            where annee = ""    --Ann�e
            group by annee, nom, prenom
        )
        minus
        (
            select annee, nom, prenom, sum(total_seconde) as tmp from tdf_coureur co
            join tdf_parti_coureur using (n_coureur)
            join tdf_abandon using (n_coureur, annee)
            join tdf_temps using (n_coureur, annee)
            where annee = ""    --Ann�e
            group by annee, nom, prenom
        )
        order by tmp
    )
    where tmp <=
    (
        select sum(total_seconde) as tmp from tdf_coureur co
        join tdf_parti_coureur using (n_coureur)
        join tdf_temps using (n_coureur, annee)
        where n_coureur =""     --n_coureur
        and annee = ""      --Ann�e
        group by annee, nom, prenom
    );
    
    --Recherche du type d'abandon et de l'�tape :
    select libelle, n_epreuve from tdf_coureur
    join tdf_parti_coureur using (n_coureur)
    join tdf_abandon using (n_coureur, annee)
    join tdf_typeaban using (c_typeaban)
    where annee = 1994
    and n_coureur = 28;
    
    select n_coureur as "Numero de coureur", co.nom as "Nom", prenom as "Prenom", annee_naissance as "Annee de naissance", annee_prem as "Annee de premi�re", na.nom as "Nation"
    from tdf_coureur co
    join tdf_app_nation using (n_coureur)
    join tdf_nation na using (code_cio)
    where n_coureur = 1354;
            
--Requ�te qui r�cup�re juste les coureurs n'ayant jamais particip� au tdf :
select n_coureur, nom, prenom from tdf_coureur
where n_coureur not in 
(
    select n_coureur from tdf_parti_coureur
)
order by n_coureur;

select * from tdf_coureur order by n_coureur desc;
where nom = 'ALONSO MONJE';

--supprimer un coureur :
delete from tdf_app_nation where n_coureur=1774;
delete from tdf_coureur where n_coureur=1774;


update tdf_coureur set nom = 'GROUT' where nom = 'GROUT';


insert into tdf_app_nation (n_coureur,code_cio) values (1772, 
    (
        select code_cio from tdf_nation where nom ='FRANCE'
    )
);


select * from tdf_coureur where n_coureur = '500';



--Les �tapes + le gagnant
select annee from tdf_annee order by annee; --Permet de choisir l'ann�e
select n_epreuve from tdf_etape where annee = 2012 order by n_epreuve; --permet de savoir combien il y a d'�tape pour une ann�e donn�e

--Requ�te qui r�cup�re le temps total pour chaque �tape en fonction du coureur :
select n_epreuve, distance, jour, heure, minute, seconde, nom, prenom, total_seconde from tdf_etape
join tdf_temps using (annee, n_epreuve)
join tdf_coureur using (n_coureur)
where annee = 2018
and n_epreuve = 1
and total_seconde >= all
(
    select total_seconde from tdf_etape
    join tdf_temps using (annee, n_epreuve)
    join tdf_coureur using (n_coureur)
    where annee = 2018
    and n_epreuve = 1
)
order by n_epreuve, total_seconde; ---pas la bonne il faut utiliser le rang d'arriv�

--requete avec le rang d'arriv�e :
select n_epreuve, nom, prenom, rang_arrivee, heure, minute, seconde, total_seconde from tdf_etape
join tdf_temps using (annee, n_epreuve)
join tdf_coureur using (n_coureur)
where annee = 1986
and rang_arrivee = 1
order by n_epreuve;


select n_epreuve, count(*) as nb from tdf_etape
join tdf_temps using (annee, n_epreuve)
where annee = 1986
and rang_arrivee = 1
group by n_epreuve
order by n_epreuve;


select count(*) from tdf_coureur 
join tdf_app_nation using (n_coureur)
where nom = '.$_POST['nomCoureur'].'
and prenom = '.$_POST['prenomCoureur'].'
and code_cio = '.$_POST['nationCoureur']';


select co.nom as "Nom du Coureur", co.prenom as "Prenom du Coureur", sp.nom as "Nom du Sponsor" from tdf_coureur co
join tdf_parti_coureur using (n_coureur)
join tdf_sponsor sp using (n_equipe, n_sponsor)
where annee = 2018
order by sp.nom;

--requete pr les pays des sponsors :
select distinct tdf_nation.nom from tdf_nation 
join tdf_sponsor using (code_cio) order by nom;

--requete pour les pays des coureurs :
select distinct tdf_nation.nom from tdf_nation 
join tdf_app_nation using (code_cio)
join tdf_coureur using (n_coureur)
order by nom;

--Toutes :
select distinct Nom from
(
    select distinct tdf_nation.nom as Nom from tdf_nation 
    join tdf_sponsor using (code_cio)
    union
    select distinct tdf_nation.nom as Nom from tdf_nation 
    join tdf_app_nation using (code_cio)
    join tdf_coureur using (n_coureur)
)
order by nom;

--pays tjr existant :
select distinct Nom from
(
    select distinct tdf_nation.nom as Nom from tdf_nation 
    join tdf_sponsor using (code_cio)
    where annee_disparition is null
    union
    select distinct tdf_nation.nom as Nom from tdf_nation 
    join tdf_app_nation using (code_cio)
    join tdf_coureur using (n_coureur)
    where annee_disparition is null
)
order by nom;


--ajout sponsor :
select * 
from tdf_sponsor 
where nom= 'AUBER 93939393'
order by annee_sponsor desc;


commit;

Insert into tdf_sponsor(n_equipe, n_sponsor, nom, na_sponsor, code_cio, annee_sponsor)
values(:n_equipe, (select max(n_sponsor) from tdf_sponsor)+1,:nomSpon, :nas, :cio, :annee);

Insert into tdf_sponsor(n_equipe, n_sponsor, nom, na_sponsor, code_cio, annee_sponsor)
				values(:n_equipe, (select max(n_sponsor) from tdf_sponsor)+1,:nomSpon, :nas, :cio, :annee);


select count(*) from tdf_sponsor 
where na_sponsor = :na
and nom = :nom
and code_cio = :cio;

select n_equipe, n_sponsor, nom, na_sponsor, code_cio,annee_sponsor 
from tdf_sponsor where (n_equipe, n_sponsor) in
(
    select n_equipe, max(n_sponsor)
    from tdf_sponsor
    group by n_equipe
)
order by n_equipe;

select * from ;


select * from tdf_classements_generaux;


--recupe des infos sur un coureur :
select * from tdf_coureur;
(
    select n_coureur, tdf_coureur.nom as Nom, prenom, annee_naissance, tdf_nation.nom as Pays, count(*) as "Nb Participation" from tdf_coureur
    join tdf_app_nation using (n_coureur)
    join tdf_nation using (code_cio)
    join tdf_parti_coureur using (n_coureur)
    group by n_coureur, tdf_coureur.nom, prenom, annee_naissance, tdf_nation.nom
)union(
    (
        select n_coureur, tdf_coureur.nom as Nom, prenom, annee_naissance, tdf_nation.nom as Pays, 0 as "Nb Participation" from tdf_coureur
        join tdf_app_nation using (n_coureur)
        join tdf_nation using (code_cio)
    )minus(
        select n_coureur, tdf_coureur.nom as Nom, prenom, annee_naissance, tdf_nation.nom as Pays, 0 as "Nb Participation" from tdf_coureur
        join tdf_app_nation using (n_coureur)
        join tdf_nation using (code_cio)
        join tdf_parti_coureur using (n_coureur)
        group by n_coureur, tdf_coureur.nom, prenom, annee_naissance, tdf_nation.nom
    )
)
order by n_coureur desc;


select count(*) from tdf_coureur
join TDF_PARTI_COUREUR using (n_coureur)
where n_coureur = 1584;

----------------------------- a lancer une fois r�visions termin�es
drop table ten_match;
drop table ten_joueur;

create table ten_joueur(numJoueur number(3),nom  char(12),prenom char(12), encourse number(1));
create table ten_match (numMatch number(3),numJoueur_1 number(3),numJoueur_2 number(3), resultat number(1));

insert into ten_joueur values (1, 'connors ','Sylvian',1);
insert into ten_joueur values (2, 'porcq ','Bjorn',1);
insert into ten_joueur values (3, 'noah ','Didier',1);
insert into ten_joueur values (4, 'santoro ','Laurent',1);
insert into ten_joueur values (5, 'mcenroe ','Samir',1);
insert into ten_joueur values (6, 'nadal ','Robert',1);
insert into ten_joueur values (7, 'federer ','Fabienne',1);
insert into ten_joueur values (8, 'roddick ','Christelle',1);
commit;


select * from ten_joueur;
select * from ten_match;

-----------------------------



