/************************************************************
				CREATION DES DOMAINES
************************************************************/
CREATE DOMAIN type_abo_dom varchar
   CHECK (VALUE IN ('annuel', 'mensuel', 'aucun'));
CREATE DOMAIN etat_res_dom varchar
   CHECK (VALUE IN ('annulee', 'reservee', 'effectuee'));
CREATE DOMAIN niveau_dom varchar
   CHECK (VALUE IN ('debutant', 'intermediaire', 'expert'));
CREATE DOMAIN nom_statut_dom varchar
   CHECK (VALUE IN ('ROLE_COACH', 'ROLE_EMPLOYEE', 'ROLE_CLIENT', 'ROLE_ADMIN'));
/************************************************************
		CREATION DES TABLES
************************************************************/
CREATE EXTENSION pgcrypto;
CREATE TABLE Statut (
	id_statut		SERIAL PRIMARY KEY,
	nom_statut		nom_statut_dom NOT NULL
);
CREATE TABLE Utilisateur (
	id_utilisateur			SERIAL PRIMARY KEY,
	nom_utilisateur		varchar NOT NULL,
	prenom_utilisateur		varchar NOT NULL,
	date_naiss_utilisateur		date NOT NULL,
	actif				boolean NOT NULL DEFAULT TRUE,
	demande_relance		boolean,
	delai_relance			int,
	date_derniere_activite	date,
	id_statut 			integer REFERENCES Statut(id_statut)
);
CREATE TABLE Connexion (
  	id_connexion		SERIAL PRIMARY KEY,
    	id_utilisateur		integer REFERENCES Utilisateur(id_utilisateur),
    	email			varchar UNIQUE NOT NULL,
    	password      		varchar NOT NULL       
);
CREATE TABLE Carte (
	id_carte		SERIAL PRIMARY KEY,
	seance_dispo		integer NOT NULL,
	active 			boolean NOT NULL DEFAULT TRUE,
	id_utilisateur 		integer REFERENCES Utilisateur(id_utilisateur)
);
CREATE TABLE Abonnement (
	id_abonnement		SERIAL PRIMARY KEY,
	type_abo			type_abo_dom NOT NULL,
	date_fin_abo			date NOT NULL,
    	id_utilisateur 			integer REFERENCES Utilisateur(id_utilisateur)
);
CREATE TABLE Activite (
	id_activite		SERIAL PRIMARY KEY,
	nom_activite		varchar NOT NULL
);
CREATE TABLE Seance (
	id_seance			SERIAL PRIMARY KEY,
	type_seance			varchar NOT NULL,
	capacite_seance		integer CHECK (capacite_seance >= 0),
places_restantes		integer NOT NULL ,
	niveau_seance		niveau_dom NOT NULL,
	avec_coach			boolean NOT NULL,
	date_seance			date NOT NULL,	
	heure_seance			time NOT NULL,
	id_activite			integer REFERENCES Activite(id_activite),
	id_coach			integer REFERENCES Utilisateur(id_utilisateur)
);
CREATE TABLE Reservation_Interne (
	id_reservation			SERIAL PRIMARY KEY,
	etat_reservation		etat_res_dom NOT NULL,
	id_utilisateur 			integer NOT NULL,
	id_seance 			integer REFERENCES Seance(id_seance)
);
CREATE TABLE Reservation_Externe (
	id_reservation 			SERIAL PRIMARY KEY,
	etat_reservation			varchar NOT NULL,
	id_utilisateur_externe			integer NOT NULL,
	id_seance 				integer REFERENCES Seance(id_seance)
);
CREATE TABLE Seance_Archivage (
	id_seance			SERIAL PRIMARY KEY,
	type_seance			varchar NOT NULL,
	capacite_seance		integer NOT NULL CHECK (capacite_seance >= 0),
	niveau_seance		varchar NOT NULL,
	avec_coach			boolean NOT NULL,
	date_seance			date NOT NULL,	
	heure_seance			time NOT NULL,
	id_activite			integer REFERENCES Activite(id_activite),
id_coach			integer REFERENCES Utilisateur(id_utilisateur)
);
CREATE TABLE Reservation_Interne_Archivage (
	id_reservation			SERIAL PRIMARY KEY,
	id_utilisateur 			integer REFERENCES Utilisateur(id_utilisateur),
	id_seance_archivage 		integer REFERENCES Seance_Archivage(id_seance)
);
CREATE TABLE Reservation_Externe_Archivage (
	id_reservation			SERIAL PRIMARY KEY,
	id_utilisateur_externe		integer NOT NULL,
	id_seance_archivage 		integer REFERENCES Seance_Archivage(id_seance)
);
/*************************************************************
		INSERTION DANS STATUT
*************************************************************/
INSERT INTO Statut VALUES (1, 'ROLE_EMPLOYEE');
INSERT INTO Statut VALUES (2, 'ROLE_COACH');
INSERT INTO Statut VALUES (3, 'ROLE_ADMIN');
INSERT INTO Statut VALUES (4, 'ROLE_CLIENT');
    




/*************************************************************
	CREATION FONCTIONS
*************************************************************/


CREATE OR REPLACE FUNCTION nb_seances_archivees() RETURNS bigint AS $$
BEGIN
	Select Count(*) FROM seance_archivage;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION nb_coach() RETURNS bigint AS $$
BEGIN
	Select Count(*) FROM utilisateur WHERE actif = true AND id_statut = 2;
END;
$$ LANGUAGE plpgsql;

CREATE FUNCTION nb_reservations_internes_archivees() RETURNS bigint AS $$
BEGIN
	Select Count(*) FROM reservation_interne_archivage;
END;
$$ LANGUAGE plpgsql;

CREATE FUNCTION nb_reservations_externes_archivees() RETURNS bigint AS $$
BEGIN
Select Count(*) FROM reservation_externe_archivage;
END;
$$ LANGUAGE plpgsql;

----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION new_reservation_interne(idUser int, idSeance int) returns void AS $$
	BEGIN 
	----------------------------------------------------------------------------------------------
	--Fonction qui permet d'enregistrer une nouvelle reservation interne
    	INSERT INTO reservation_interne(etat_reservation,id_utilisateur,id_seance)
        VALUES('reservee',idUser,idSeance);
	END;
$$ LANGUAGE 'plpgsql';

----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION new_reservation_externe(idUser int, idSeance int) returns void AS $$
	BEGIN 
	----------------------------------------------------------------------------------------------
	--Fonction qui permet d'enregistrer une nouvelle reservation externe
    	INSERT INTO reservation_externe(etat_reservation,id_utilisateur_externe,id_seance)
        VALUES('reservee',idUser,idSeance);
	END;
$$ LANGUAGE 'plpgsql';

----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION annulation_reservation_externe(idReservation int) returns void AS $$
	BEGIN 
	----------------------------------------------------------------------------------------------
	--Fonction qui permet d'annuler une reservation externe
    	UPDATE reservation_externe
		SET etat_reservation = 'annulee'
		WHERE id_reservation = idReservation;
	END;
$$ LANGUAGE 'plpgsql';

----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION annulation_reservation_interne(idReservation int) returns void AS $$
	BEGIN 
	----------------------------------------------------------------------------------------------
	--Fonction qui permet d'annuler une reservation interne
    	UPDATE reservation_interne
		SET etat_reservation = 'annulee'
		WHERE id_reservation = idReservation;
	END;
$$ LANGUAGE 'plpgsql';

----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION supprimer_reservation_externe(idReservation int) returns void AS $$
    BEGIN 
	----------------------------------------------------------------------------------------------
	--Fonction qui permet de supprimer une reservation externe (à partir de son id)
        DELETE from reservation_externe
        WHERE id_reservation = idReservation;
    END;
$$ LANGUAGE 'plpgsql';

----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION supprimer_reservation_interne(idReservation int) returns void AS $$
    BEGIN 
	----------------------------------------------------------------------------------------------
	--Fonction qui permet de supprimer une reservation interne (à partir de son id)
        DELETE from reservation_interne
        WHERE id_reservation = idReservation;
    END;
$$ LANGUAGE 'plpgsql';

----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION supprimer_seance(idSeancce int) returns void AS $$
    BEGIN
	----------------------------------------------------------------------------------------------
	--Fonction qui permet de supprimer une seance (à partir de son id) 
        DELETE from seance
        WHERE id_seance = idSeancce;
    END;
$$ LANGUAGE 'plpgsql';

----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION new_activite(nom varchar) returns void AS $$
	BEGIN 
	----------------------------------------------------------------------------------------------
	--Fonction qui permet de créer une activite
    	Insert into activite (nom_activite) values (nom);
	END;
$$ LANGUAGE 'plpgsql';


----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION new_abo(type_abo varchar,id_utilisateur int) returns void AS $$
	DECLARE
    	date_fin date;
    BEGIN 
	----------------------------------------------------------------------------------------------
	--Fonction qui permet d'ajouter un abonnement à un utilisateur
    	if (type_abo = 'mensuel') Then
        	date_fin = current_date + interval '1 month';
        else
        	date_fin = current_date + interval '12 month';
        end if;
		insert into abonnement (type_abo,date_fin_abo,id_utilisateur) values (type_abo,date_fin,id_utilisateur);
	END;
$$ LANGUAGE 'plpgsql';



----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION new_carte(utilisateur int) returns void AS $$
    BEGIN 
	----------------------------------------------------------------------------------------------
	--Fonction qui permet d'ajouter une carte 10 places a un utilisateur
		insert into carte(seance_dispo,active,id_utilisateur) values (10,true,utilisateur);
	END;
$$ LANGUAGE 'plpgsql';


----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION new_seance (type_s varchar, capacite integer,niveau varchar,coach boolean,date_s date,
                                       heure time with time zone,activite integer) returns void AS $$
	BEGIN 
	----------------------------------------------------------------------------------------------
	--Fonction qui permet d'ajouter une nouvelle seance
    	insert into seance (type_seance,capacite_seance,niveau_seance,avec_coach,
                            date_seance,heure_seance,id_activite,places_restantes)
                            values (type_s,capacite,niveau,coach,date_s,heure,activite,capacite);
	END;
$$ LANGUAGE 'plpgsql';


----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION new_seance_archivage (
    identifiant int, type_s varchar, capacite integer,niveau varchar,coach boolean,
    date_s date,heure time with time zone,activite integer,idcoach integer) returns void AS $$
    BEGIN 
	--Fonction qui permet d'ajouter une nouvelle seance_archivage
	--On passe l'id de la seance afin de connaitre directement son identifiant pour 
	--Archiver les reservations de la seance
        insert into seance_archivage (id_seance, type_seance,capacite_seance,niveau_seance,avec_coach,
                            date_seance,heure_seance,id_activite
                            ,id_coach)
                            values (identifiant, type_s,capacite,niveau,coach,date_s,heure,activite, idcoach);
    END;
$$ LANGUAGE 'plpgsql';

----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION new_reservation_externe_archivage(idUser int, idSeance int) returns void AS $$
	BEGIN 
	----------------------------------------------------------------------------------------------
	--Fonction qui permet d'ajouter une nouvelle reservation externe archivage
    	INSERT INTO reservation_externe_archivage(id_utilisateur_externe,id_seance_archivage)
        VALUES(idUser,idSeance);
	END;
$$ LANGUAGE 'plpgsql';

----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION new_reservation_interne_archivage(idUser int, idSeance int) returns void AS $$
	BEGIN 
	----------------------------------------------------------------------------------------------
	--Fonction qui permet d'ajouter une nouvelle reservation interne archivage
    	INSERT INTO reservation_interne_archivage(id_utilisateur,id_seance_archivage)
        VALUES(idUser,idSeance);
	END;
$$ LANGUAGE 'plpgsql';


----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION new_utilisateur(nom varchar,prenom varchar,date_naiss date,
                           demande boolean,delai integer,id_statut_utilisateur varchar) RETURNS void AS $$
	DECLARE 
	BEGIN 
		----------------------------------------------------------------------------------------------
		--Fonction qui permet d'ajouter un nouvel utilisateur
		INSERT INTO 
   		Utilisateur (nom_utilisateur, prenom_utilisateur, date_naiss_utilisateur, actif, demande_relance, delai_relance,date_derniere_activite, id_statut) 
    	VALUES (nom, prenom, date_naiss,true, demande, delai,current_date, id_statut_utilisateur);
	END;
$$ LANGUAGE 'plpgsql';

----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION public.calcul_places_reservees( idseance integer)
    RETURNS integer AS $$
     DECLARE
    nbInterne int;
        nbExterne int;
        nb int;
    BEGIN
	----------------------------------------------------------------------------------------------
	--Fonction qui calcul le nombres de palces reservees sur une seance
	--Ou le nombre de places qui ont été reservees (en etat effectuee) si la seance est passée et non archivée
        Select count(*) into nbInterne
        from reservation_interne
        where (etat_reservation = 'reservee' OR etat_reservation = 'effectuee')
        AND id_seance = idSeance;
        
        Select count(*) into nbExterne
        from reservation_externe
        where (etat_reservation = 'reservee' OR etat_reservation = 'effectuee')
        AND id_seance = idSeance;
        
        nb = nbInterne + nbExterne;
        return nb;
    END;
$$ LANGUAGE plpgsql;


----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION maj_etat_carte() RETURNS trigger AS $$
DECLARE
    nb integer;
BEGIN
	----------------------------------------------------------------------------------------------
	--Fonction qui change l'état de la carte selon le nombre de places restantes dessus
	--Executee dans un trigger
	if new.seance_dispo <> old.seance_dispo then
        --On récupère le nb de places restantes sur la carte modifiée
        if new.seance_dispo = 0 then 
            new.active = false;
        else 
            new.active = true;
        end if;
	end if;
    return new;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER maj_etat_carte BEFORE UPDATE ON carte
FOR EACH ROW 
EXECUTE PROCEDURE maj_etat_carte();


----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION maj_places_restantes() RETURNS TRIGGER AS $$
DECLARE
    nb int;
    capacite int;
    rest int;
BEGIN
	----------------------------------------------------------------------------------------------
	--Fonction qui permet de mettre à jour le nombre de places restantes sur une seance
	--Executee dans un trigger
		-- On stocke le nb de place reservée sur la séance concernée par la reservation
		nb = calcul_places_reservees(new.id_seance);
        -- On stocke éaglement la capacité de cette séance
        SELECT seance.capacite_seance into capacite
        FROM seance
        WHERE seance.id_seance = new.id_seance;
        
        -- On calcule le nombre de places restantes
        rest = capacite - nb;
        
        --On met à jour les places de la séance concernée
        UPDATE seance SET places_restantes = rest WHERE seance.id_seance = new.id_seance;
	RETURN NULL;
END;
$$ LANGUAGE plpgsql;

----------------------------------------------------------------------------------------------
--Trigger qui met a jour le nombre de places d'une seance quand il y a une nouvelle reservation_interne
--Ou qu'une reservation interne est modifiée

CREATE TRIGGER maj_places_restantes 
AFTER INSERT OR UPDATE ON reservation_interne
FOR EACH ROW
EXECUTE PROCEDURE maj_places_restantes();

----------------------------------------------------------------------------------------------
--Trigger qui met a jour le nombre de places d'une seance quand il y a une nouvelle reservation_externe
--Ou qu'une reservation externe est modifiée

CREATE TRIGGER maj_places_restantes 
AFTER INSERT OR UPDATE ON reservation_externe
FOR EACH ROW
EXECUTE PROCEDURE maj_places_restantes();


----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION coach_dispo_seance(idcoach integer,dateseance date, heureseance time without time zone)
    RETURNS boolean AS $$
    DECLARE
    	une_seance seance%rowtype;
    BEGIN
	----------------------------------------------------------------------------------------------
	--Fonction qui permet de savoir si oui ou non un coach est disponible a une date et heure donnée
        for une_seance in Select * from seance where id_coach = idcoach
        loop 
        	if une_seance.date_seance = dateseance then 
            	if une_seance.heure_seance = heureseance then
                	return true;
				end if;
            end if;
        end loop;
        return false;
    END;
$$ LANGUAGE plpgsql;

----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION changementEtatReservationInterne()
    RETURNS void AS $$
    DECLARE
        une_reservation reservation_interne%rowtype;
        dateSeance date;
    BEGIN
	----------------------------------------------------------------------------------------------
	--Fonction qui permet de changer l'état des reservations internes donc la séance est passée
        --On va traiter toutes les reservations reservees pour voir si elles doivent changer d'état
        for une_reservation in Select * from reservation_interne where etat_reservation = 'reservee'
        loop 
            Select into dateSeance date_seance 
            from seance 
            where id_seance = une_reservation.id_seance;
            --Si la date est passée :
            if (dateSeance < current_date ) then 
                update reservation_interne SET etat_reservation = 'effectuee'
                where id_reservation = une_reservation.id_reservation;
            end if;
        end loop;
    END;
$$ LANGUAGE plpgsql;

----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION changementEtatReservationExterne()
    RETURNS void AS $$
    DECLARE
        une_reservation reservation_externe%rowtype;
        dateSeance date;
    BEGIN
	----------------------------------------------------------------------------------------------
	--Fonction qui permet de changer l'état des reservations externe donc la séance est passée
        --On va traiter toutes les reservations reservees pour voir si elles doivent changer d'état
        for une_reservation in Select * from reservation_externe where etat_reservation = 'reservee'
        loop 
            Select into dateSeance date_seance 
            from seance 
            where id_seance = une_reservation.id_seance;
            --Si la date est passée :
            if (dateSeance < current_date ) then 
                update reservation_externe SET etat_reservation = 'effectuee'
                where id_reservation = une_reservation.id_reservation;
            end if;
        end loop;
    END;
$$ LANGUAGE plpgsql;


----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION supprimer_reservation_seance(idSeance int)
    RETURNS void AS $$
    DECLARE
        une_reservation_interne reservation_interne%rowtype;
        une_reservation_externe reservation_externe%rowtype;
    BEGIN
	----------------------------------------------------------------------------------------------
	--Fonction qui permet de supprimer toutes les reservations interne et externe d'une seance
	-- peu importe leur état
        --On va traiter toutes reservations interne de la seance
        for une_reservation_interne in Select * from reservation_interne where id_seance = idSeance
        loop 
            PERFORM supprimer_reservation_interne(une_reservation_interne.id_reservation);
        end loop;
        
        --On va traiter toutes reservations externe de la seance
        for une_reservation_externe in Select * from reservation_externe where id_seance = idSeance
        loop 
            PERFORM supprimer_reservation_externe(une_reservation_externe.id_reservation);
        end loop;
    END;
$$ LANGUAGE plpgsql;

----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION archiver_reservation_seance(idSeance int)
    RETURNS void AS $$
    DECLARE
        une_reservation_interne reservation_interne%rowtype;
        une_reservation_externe reservation_externe%rowtype;
    BEGIN
	----------------------------------------------------------------------------------------------
	--Fonction qui permet d'achiver les reservations interne et externe effectuee d'une seance
	--Il est necessaire que le changement d'état des reservations ai été effectué auparavant
        --On va traiter toutes reservations interne de la seance
        for une_reservation_interne in Select * from reservation_interne where id_seance = idSeance
        loop 
            if une_reservation_interne.etat_reservation == 'effectuee' then
                PERFORM new_reservation_interne_archivage(une_reservation_interne.id_utilisateur,idSeance);
            end if;
        end loop;
        
        --On va traiter toutes reservations externe de la seance
        for une_reservation_externe in Select * from reservation_externe where id_seance = idSeance
        loop 
            if une_reservation_externe.etat_reservation == 'effectuee' then
                PERFORM new_reservation_externe_archivage(une_reservation_interne.id_utilisateur_externe, idSeance);
            end if;
        end loop;
    END;
$$ LANGUAGE plpgsql;

----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION changementEtatReservationInterne()
    RETURNS void AS $$
    DECLARE
        une_reservation reservation_interne%rowtype;
        dateSeance date;
    BEGIN
	----------------------------------------------------------------------------------------------
	--Fonction qui permet de changer l'état des reservations internes donc la séance est passée
        --On va traiter toutes les reservations reservees pour voir si elles doivent changer d'état
        for une_reservation in Select * from reservation_interne where etat_reservation = 'reservee'
        loop 
            Select into dateSeance date_seance 
            from seance 
            where id_seance = une_reservation.id_seance;
            --Si la date est passée :
            if (dateSeance < current_date ) then 
                update reservation_interne SET etat_reservation = 'effectuee'
                where id_reservation = une_reservation.id_reservation;
            end if;
        end loop;
    END;
$$ LANGUAGE plpgsql;

----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION changementEtatReservationExterne()
    RETURNS void AS $$
    DECLARE
        une_reservation reservation_externe%rowtype;
        dateSeance date;
    BEGIN
	----------------------------------------------------------------------------------------------
	--Fonction qui permet de changer l'état des reservations externe donc la séance est passée
        --On va traiter toutes les reservations reservees pour voir si elles doivent changer d'état
        for une_reservation in Select * from reservation_externe where etat_reservation = 'reservee'
        loop 
            Select into dateSeance date_seance 
            from seance 
            where id_seance = une_reservation.id_seance;
            --Si la date est passée :
            if (dateSeance < current_date ) then 
                update reservation_externe SET etat_reservation = 'effectuee'
                where id_reservation = une_reservation.id_reservation;
            end if;
        end loop;
    END;
$$ LANGUAGE plpgsql;


----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION supprimer_reservation_seance(idSeance int)
    RETURNS void AS $$
    DECLARE
        une_reservation_interne reservation_interne%rowtype;
        une_reservation_externe reservation_externe%rowtype;
    BEGIN
	----------------------------------------------------------------------------------------------
	--Fonction qui permet de supprimer toutes les reservations interne et externe d'une seance
	-- peu importe leur état

        --On va traiter toutes reservations interne de la seance
        for une_reservation_interne in Select * from reservation_interne where id_seance = idSeance
        loop 
            PERFORM supprimer_reservation_interne(une_reservation_interne.id_reservation);
        end loop;
        
        --On va traiter toutes reservations externe de la seance
        for une_reservation_externe in Select * from reservation_externe where id_seance = idSeance
        loop 
            PERFORM supprimer_reservation_externe(une_reservation_externe.id_reservation);
        end loop;
    END;
$$ LANGUAGE plpgsql;

----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION archiver_reservation_seance(idSeance int)
    RETURNS void AS $$
    DECLARE
        une_reservation_interne reservation_interne%rowtype;
        une_reservation_externe reservation_externe%rowtype;
    BEGIN
	----------------------------------------------------------------------------------------------
	--Fonction qui permet d'achiver les reservations interne et externe effectuee d'une seance
	--Il est necessaire que le changement d'état des reservations ai été effectué auparavant
        --On va traiter toutes reservations interne de la seance
        for une_reservation_interne in Select * from reservation_interne where id_seance = idSeance
        loop 
            if une_reservation_interne.etat_reservation = 'effectuee' then
                PERFORM new_reservation_interne_archivage(une_reservation_interne.id_utilisateur,idSeance);
            end if;
        end loop;
        
        --On va traiter toutes reservations externe de la seance
        for une_reservation_externe in Select * from reservation_externe where id_seance = idSeance
        loop 
            if une_reservation_externe.etat_reservation = 'effectuee' then
                PERFORM new_reservation_externe_archivage(une_reservation_interne.id_utilisateur_externe, idSeance);
            end if;
        end loop;
    END;
$$ LANGUAGE plpgsql;

----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION archivageSeance()
    RETURNS void AS $$
    DECLARE
        une_seance seance%rowtype;
        bol boolean;
    BEGIN
	----------------------------------------------------------------------------------------------
	--Fonction qui permet d'archiver ou supprimer les seance passée
	--Ainsi que les reservations correspondantes
        --On va traiter toutes les seances
        raise notice 'On traite toutes les seances';
        for une_seance in Select * from seance
        loop 
            --Si la date de la seance est passée : 
            if (une_seance.date_seance < current_date ) then 
             --S'il y a bien des reservations
                if(une_seance.capacite_seance > une_seance.places_restantes) then
                    --On archive tout d'abord la seance
                     PERFORM new_seance_archivage (une_seance.id_seance,une_seance.type_seance,une_seance.capacite_seance,
                                    une_seance.niveau_seance, une_seance.avec_coach,une_seance.date_seance,
                                    une_seance.heure_seance,une_seance.id_activite,une_seance.id_coach);
                    --On archive ensuite les reservatons de cette seance
                    PERFORM archiver_reservation_seance(une_seance.id_seance);
                end if;
                --Dans tous les cas on supprime ensuite toutes les reservations, ainsi que la seance
                PERFORM supprimer_reservation_seance(une_seance.id_seance);
                PERFORM supprimer_seance(une_seance.id_seance);
            end if;
        end loop;
    END;
$$ LANGUAGE plpgsql;






/*************************************************************
	CREATION DES DONNEES
*************************************************************/
/********************* UTILISATEUR ****************/
/*Employé*/
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Leroy','Honorine','02/04/1995',true,null,null,null,1);
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Dubois', 'Eugene','27/04/1990',true, null, null, null, 1);
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('François', 'Marie','01/01/1990',false, null, null, null, 1);


/* Coach */
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Petit', 'Amandine','27/04/1992',true, null, null, null,2);
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Thomas', 'Sandrine','27/03/1994',true, null, null, null,2);
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Smith', 'Laura','22/12/1984',false, null, null, null,2);


/* Admin */
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Lambert','Thomas','29/09/1988',true, null, null, null,3);
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Humbert','Nicolas','08/07/1984',false, null, null, null,3);


/* Client */
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Smith', 'John', '29/05/1995', true, true, 7, '11/10/2016',  4);
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Dupont', 'Christophe', '25/8/1987', false, true, 7, '30/11/2016', 4);
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Barbier', 'Clément', '31/12/1990', true, true, 7, '02/01/2018',4);
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Martin', 'Jeanne', '24/03/1997', true, true, 7, '31/01/2017', 4);
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Bernard', 'Olivier', '12/12/1982', false, true, 7, '28/02/2016', 4);
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Fernandes', 'Charlotte', '16/03/1995', true, true, 7, '02/01/2017', 4);
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Lamblin', 'Nicolas', '13/05/1996', true, true, 7, '02/01/2017', 4);
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Mansuy', 'Claire', '20/11/1996', true, true, 7, '02/01/2017', 4);
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Thiriot', 'Anais', '27/12/1995', true, true, 7, '02/01/2017', 4);


/* Autres coachs */
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Bernard', 'Amandine','27/04/1989',true, null, null, null,2);
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Thomas', 'Louis','27/01/1994',true, null, null, null,2);
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Smith', 'Jacky','11/11/1982',false, null, null, null,2);
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Four', 'Lou','27/04/1989',true, null, null, null,2);
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Andres', 'Paul','27/01/1994',true, null, null, null,2);
insert into utilisateur (nom_utilisateur,prenom_utilisateur,date_naiss_utilisateur,actif,demande_relance,delai_relance,date_derniere_activite,id_statut)
values('Antoine', 'Mary','11/11/1982',false, null, null, null,2);



/************** CONNEXION ****************/
/*Employe*/
insert into connexion (id_utilisateur,email,password)
values(1,'honorine.leroy@hotmail.fr',crypt('honorine.leroy', gen_salt('bf',8)));
insert into connexion (id_utilisateur,email,password)
values(2,'eugene.dubois@hotmail.fr',crypt('eugene.dubois', gen_salt('bf',8)));
insert into connexion (id_utilisateur,email,password)
values(3,'marie.francois@hotmail.fr',crypt('marie.francois', gen_salt('bf',8)));
/*Coach */
insert into connexion (id_utilisateur,email,password)
values(4,'amandine.petit@hotmail.fr',crypt('amandine.petit', gen_salt('bf',8)));
insert into connexion (id_utilisateur,email,password)
values(5,'sandrine.thomas@hotmail.fr',crypt('sandrine.thomas', gen_salt('bf',8)));
insert into connexion (id_utilisateur,email,password)
values(6,'laura.smith@hotmail.fr',crypt('laura.smith', gen_salt('bf',8)));
/*Admin */
insert into connexion (id_utilisateur,email,password)
values(7,'thomas.lambert@hotmail.fr',crypt('thomas.lambert', gen_salt('bf',8)));
insert into connexion (id_utilisateur,email,password)
values(8,'nicolas.humbert@hotmail.fr',crypt('nicolas.humbert', gen_salt('bf',8)));
/*Clients*/
insert into connexion (id_utilisateur,email,password)
values(9,'john.smith@hotmail.fr',crypt('john.smith', gen_salt('bf',8)));
insert into connexion (id_utilisateur,email,password)
values(10,'christophe.dupont@hotmail.fr',crypt('christophe.dupont', gen_salt('bf',8)));
insert into connexion (id_utilisateur,email,password)
values(11,'clement.barbier@hotmail.fr',crypt('clement.barbier', gen_salt('bf',8)));
insert into connexion (id_utilisateur,email,password)
values(12,'jeanne.martin@hotmail.fr',crypt('jeanne.martin', gen_salt('bf',8)));
insert into connexion (id_utilisateur,email,password)
values(13,'olivier.bernard@hotmail.fr',crypt('olivier.bernard', gen_salt('bf',8)));
insert into connexion (id_utilisateur,email,password)
values(14,'fernandescharlotte54@gmail.com',crypt('charlotte.fernandes', gen_salt('bf',8)));
insert into connexion (id_utilisateur,email,password)
values(15,'nicolas.lamblin2@gmail.com',crypt('nicolas.lamblin', gen_salt('bf',8)));
insert into connexion (id_utilisateur,email,password)
values(16,'clmansuy@laposte.net',crypt('claire.mansuy', gen_salt('bf',8)));
insert into connexion (id_utilisateur,email,password)
values(17,'anaisthiriot@hotmail.fr',crypt('anais.thiriot', gen_salt('bf',8)));
/*Autres coach*/
insert into connexion (id_utilisateur,email,password)
values(18,'amandine.bernard@hotmail.fr',crypt('amandine.bernard', gen_salt('bf',8)));
insert into connexion (id_utilisateur,email,password)
values(19,'louis.thomas@hotmail.fr',crypt('louis.thomas', gen_salt('bf',8)));
insert into connexion (id_utilisateur,email,password)
values(20,'jacky.smith@hotmail.fr',crypt('jacky.smith', gen_salt('bf',8)));
insert into connexion (id_utilisateur,email,password)
values(21,'lou.four@hotmail.fr',crypt('lou.four', gen_salt('bf',8)));
insert into connexion (id_utilisateur,email,password)
values(22,'paul.andres@hotmail.fr',crypt('paul.andres', gen_salt('bf',8)));
insert into connexion (id_utilisateur,email,password)
values(23,'mary.antoine@hotmail.fr',crypt('mary.antoine', gen_salt('bf',8)));




/********** ABONNEMENT ********/
insert into Abonnement (type_abo,date_fin_abo,id_utilisateur)
values('mensuel','22/11/2017',9);
insert into Abonnement (type_abo,date_fin_abo,id_utilisateur)
values('annuel','01/01/2019',11);
insert into Abonnement (type_abo,date_fin_abo,id_utilisateur)
values('mensuel','22/06/2018',12);
insert into Abonnement (type_abo,date_fin_abo,id_utilisateur)
values('annuel','01/07/2018',14);


/********** CARTE ********/
insert into Carte (seance_dispo,active,id_utilisateur)
values(1,true,15);
insert into Carte (seance_dispo,active,id_utilisateur)
values(0,false,16);
insert into Carte (seance_dispo,active,id_utilisateur)
values(10,true,17);


/********** ACTIVITES ***********/
insert into activite (nom_activite) values ('Aquagym');
insert into activite (nom_activite) values ('Boxe');
insert into activite (nom_activite) values ('Zumba');
insert into activite (nom_activite) values ('Body pump');
insert into activite (nom_activite) values ('RPM');
insert into activite (nom_activite) values ('Body attack');
insert into activite (nom_activite) values ('Body combat');
insert into activite (nom_activite) values ('Yoga');
insert into activite (nom_activite) values ('Pilates');
insert into activite (nom_activite) values ('Step');
insert into activite (nom_activite) values ('Gym douce');
insert into activite (nom_activite) values ('Body Training');
insert into activite (nom_activite) values ('Stretching');
insert into activite (nom_activite) values ('Hiit workout');


/**** Premières seances qui seront archivées ****/

/* Collectives */
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,10,'debutant',true,'25/12/2017','16:00',1,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',20,20,'expert',false,'25/12/2017','18:00',3,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,10,'intermediaire',true,'25/12/2017','20:00',4,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,10,'debutant',true,'26/12/2017','16:00',5,18);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',6,6,'expert',false,'26/12/2017','18:00',6,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',8,8,'debutant',true,'26/12/2017','20:00',7,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',5,5,'intermediaire',true,'27/12/2017','16:00',8,19);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,10,'expert',false,'27/12/2017','18:00',10,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',5,5,'debutant',true,'27/12/2017','20:00',11,23);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',5,5,'intermediaire',true,'28/12/2017','16:00',9,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,10,'debutant',true,'28/12/2017','18:00',12,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,10,'intermediaire',true,'28/12/2017','20:00',13,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',8,8,'expert',false,'29/12/2017','16:00',14,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,10,'intermediaire',true,'29/12/2017','18:00',1,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,10,'debutant',true,'29/12/2017','20:00',2,11);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,10,'debutant',true,'30/12/2018','16:00',4,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,10,'expert',false,'30/12/2017','18:00',5,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',8,8,'intermediaire',true,'30/12/2017','20:00',2,21);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,10,'debutant',true,'01/01/2018','16:00',1,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',20,20,'expert',false,'01/01/2018','18:00',3,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,10,'intermediaire',true,'01/01/2018','20:00',4,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,10,'debutant',true,'02/01/2018','16:00',5,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',6,6,'expert',false,'02/01/2018','18:00',6,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',8,8,'debutant',true,'02/01/2018','20:00',7,4);


/* Individuelles */
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'debutant',true,'25/12/2017','17:00',2,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'intermediaire',true,'25/12/2017','19:00',2,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'intermediaire',true,'26/12/2017','17:00',8,6);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'expert',false,'26/12/2017','19:00',9,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'debutant',false,'27/12/2017','17:00',14,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'intermediaire',false,'27/12/2017','19:00',2,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'debutant',true,'28/12/2017','17:00',8,18);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'intermediaire',true,'28/12/2017','19:00',7,19);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'debutant',true,'29/12/2017','17:00',13,20);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'expert',true,'29/12/2017','19:00',8,21);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'expert',true,'30/12/2017','17:00',9,22);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'debutant',true,'30/12/2017','19:00',9,23);


/************ Reservation interne pour séances qui seront archivées, ci-dessus*******/
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (9,1,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (11,1,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (14,1,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (15,2,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (16,2,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (11,3,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (17,3,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (9,4,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (12,4,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (14,4,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (16,4,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (17,4,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (11,5,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (12,5,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (9,5,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (9,6,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (11,6,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (12,6,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (14,6,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (16,6,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (17,6,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (9,7,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (14,7,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (12,8,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (17,8,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (11,9,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (16,9,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (9,10,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (14,10,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (12,11,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (15,11,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (14,12,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (11,13,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (15,14,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (16,15,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (9,16,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (17,16,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (11,17,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (9,18,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (11,18,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (12,19,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (17,19,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (15,20,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (14,21,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (17,22,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (16,23,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (9,24,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (11,25,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (12,26,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (14,27,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (15,28,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (16,29,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (17,30,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (15,31,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (16,32,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (17,33,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (14,34,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (12,35,'reservee');
insert into reservation_interne (id_utilisateur,id_seance,etat_reservation)
values (11,36,'reservee');



/*** Suite séances *****/


/********** SEANCES ***********/
/* Collectives */
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',5,0,'intermediaire',true,'03/01/2018','16:00',8,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,7,'expert',false,'03/01/2018','18:00',10,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',5,0,'debutant',true,'03/01/2018','20:00',11,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',5,1,'intermediaire',true,'04/01/2018','16:00',9,18);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,1,'debutant',true,'04/01/2018','18:00',12,19);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,0,'intermediaire',true,'04/01/2018','20:00',13,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',8,1,'expert',false,'05/01/2018','16:00',14,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,3,'intermediaire',true,'05/01/2018','18:00',1,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,5,'debutant',true,'05/01/2018','20:00',2,20);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,2,'debutant',true,'06/01/2018','16:00',4,21);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,10,'expert',false,'06/01/2018','18:00',5,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',8,8,'intermediaire',true,'06/01/2018','20:00',2,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,5,'debutant',true,'08/01/2018','16:00',1,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',20,3,'expert',false,'08/01/2018','18:00',3,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,4,'intermediaire',true,'08/01/2018','20:00',4,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,5,'debutant',true,'09/01/2018','16:00',5,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',6,0,'expert',false,'09/01/2018','18:00',6,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',8,1,'debutant',true,'09/01/2018','20:00',7,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',5,0,'intermediaire',true,'10/01/2018','16:00',8,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,3,'expert',false,'10/01/2018','18:00',10,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',5,0,'debutant',true,'10/01/2018','20:00',11,22);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',5,1,'intermediaire',true,'11/01/2018','16:00',9,23);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,1,'debutant',true,'11/01/2018','18:00',12,18);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,0,'intermediaire',true,'11/01/2018','20:00',13,19);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',8,1,'expert',false,'12/01/2018','16:00',14,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,2,'intermediaire',true,'12/01/2018','18:00',1,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,6,'debutant',true,'12/01/2018','20:00',2,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,8,'debutant',true,'13/01/2018','16:00',4,18);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,10,'expert',false,'13/01/2018','18:00',5,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',8,4,'intermediaire',true,'13/01/2018','20:00',2,19);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,5,'debutant',true,'15/01/2018','16:00',1,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',20,13,'expert',false,'15/01/2018','18:00',3,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,3,'intermediaire',true,'15/01/2018','20:00',4,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,5,'debutant',true,'16/01/2018','16:00',5,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',6,0,'expert',false,'16/01/2018','18:00',6,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',8,1,'debutant',true,'16/01/2018','20:00',7,20);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',5,0,'intermediaire',true,'17/01/2018','16:00',8,21);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,3,'expert',false,'17/01/2018','18:00',10,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',5,0,'debutant',true,'17/01/2018','20:00',11,22);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',5,1,'intermediaire',true,'18/01/2018','16:00',9,23);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,1,'debutant',true,'18/01/2018','18:00',12,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,0,'intermediaire',true,'18/01/2018','20:00',13,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',8,1,'expert',false,'19/01/2018','16:00',14,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,2,'intermediaire',true,'19/01/2018','18:00',1,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,6,'debutant',true,'19/01/2018','20:00',2,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,8,'debutant',true,'20/01/2018','16:00',4,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,10,'expert',false,'20/01/2018','18:00',5,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',8,4,'intermediaire',true,'20/01/2018','20:00',2,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,5,'debutant',true,'22/01/2018','16:00',1,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',20,13,'expert',false,'22/01/2018','18:00',3,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,3,'intermediaire',true,'22/01/2018','20:00',4,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,5,'debutant',true,'23/01/2018','16:00',5,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',6,0,'expert',false,'23/01/2018','18:00',6,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',8,1,'debutant',true,'23/01/2018','20:00',7,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',5,0,'intermediaire',true,'24/01/2018','16:00',8,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,3,'expert',false,'24/01/2018','18:00',10,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',5,0,'debutant',true,'24/01/2018','20:00',11,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',5,1,'intermediaire',true,'25/01/2018','16:00',9,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,1,'debutant',true,'25/01/2018','18:00',12,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,0,'intermediaire',true,'25/01/2018','20:00',13,19);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',8,1,'expert',false,'26/01/2018','16:00',14,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,2,'intermediaire',true,'26/01/2018','18:00',1,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,6,'debutant',true,'26/01/2018','20:00',2,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,8,'debutant',true,'27/01/2018','16:00',4,22);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,10,'expert',false,'27/01/2018','18:00',5,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',8,4,'intermediaire',true,'27/01/2018','20:00',2,23);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',5,0,'intermediaire',true,'28/01/2018','16:00',8,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,3,'expert',false,'28/01/2018','18:00',10,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',5,0,'debutant',true,'28/01/2018','20:00',11,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',5,1,'intermediaire',true,'29/01/2018','16:00',9,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,1,'debutant',true,'29/01/2018','18:00',12,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,0,'intermediaire',true,'29/01/2018','20:00',13,19);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',8,1,'expert',false,'30/01/2018','16:00',14,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,2,'intermediaire',true,'30/01/2018','18:00',1,5);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,6,'debutant',true,'28/01/2018','20:00',2,4);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('collective',10,8,'debutant',true,'30/01/2018','16:00',4,22);






/* Individuelles */
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'intermediaire',true,'03/01/2018','16:00',2,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'expert',true,'03/01/2018','18:00',8,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'intermediaire',true,'04/01/2018','16:00',9,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'debutant',true,'04/01/2018','18:00',14,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'expert',true,'05/01/2018','16:00',13,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'intermediaire',true,'05/01/2018','18:00',2,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'debutant',true,'06/01/2018','16:00',8,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'expert',true,'06/01/2018','18:00',9,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'debutant',true,'08/01/2018','16:00',2,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'expert',true,'08/01/2018','18:00',2,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'debutant',true,'09/01/2018','16:00',8,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'expert',true,'09/01/2018','18:00',9,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'intermediaire',true,'10/01/2018','16:00',8,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'expert',true,'10/01/2018','18:00',13,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'intermediaire',true,'11/01/2018','16:00',9,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'debutant',true,'11/01/2018','18:00',8,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'expert',true,'12/01/2018','16:00',14,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'intermediaire',true,'12/01/2018','18:00',2,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'debutant',true,'13/01/2018','16:00',2,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'expert',true,'13/01/2018','18:00',14,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'debutant',true,'15/01/2018','16:00',13,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'expert',true,'15/01/2018','18:00',8,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'debutant',true,'16/01/2018','16:00',9,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'expert',true,'16/01/2018','18:00',8,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'intermediaire',true,'17/01/2018','16:00',8,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'expert',true,'17/01/2018','18:00',13,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'intermediaire',true,'18/01/2018','16:00',9,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'debutant',true,'18/01/2018','18:00',2,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'expert',true,'19/01/2018','16:00',14,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'intermediaire',true,'19/01/2018','18:00',2,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'debutant',true,'20/01/2018','16:00',14,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'expert',true,'20/01/2018','18:00',2,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'debutant',true,'22/01/2018','17:00',2,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'intermediaire',true,'22/01/2018','19:00',2,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'intermediaire',true,'23/01/2018','17:00',8,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'expert',true,'23/01/2018','19:00',9,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'debutant',true,'24/01/2018','17:00',14,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'intermediaire',true,'24/01/2018','19:00',2,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'debutant',true,'25/01/2018','17:00',8,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'intermediaire',true,'25/01/2018','19:00',7,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'debutant',true,'26/01/2018','17:00',13,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'expert',true,'26/01/2018','19:00',8,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,0,'expert',true,'27/01/2018','17:00',9,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'debutant',true,'27/01/2018','19:00',9,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'debutant',true,'27/01/2018','17:00',2,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'intermediaire',true,'28/01/2018','19:00',2,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'intermediaire',false,'25/01/2018','17:00',8,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'expert',true,'29/01/2018','19:00',9,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'debutant',true,'30/01/2018','17:00',14,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'intermediaire',false,'26/01/2018','19:00',2,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'debutant',false,'25/01/2018','17:00',8,null);
insert into seance (type_seance,capacite_seance,places_restantes,niveau_seance,avec_coach, date_seance,heure_seance,id_activite,id_coach)
values('individuelle',1,1,'intermediaire',false,'25/01/2018','19:00',7,null);






/************ RESERVATION INTERNE *******/
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',9,37);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('annulee',11,37);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',12,37);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',14,37);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',15,37);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',16,38);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',12,38);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('annulee',17,38);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',9,39);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',11,39);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',12,40);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',14,40);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',15,40);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',16,40);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',14,41);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',15,41);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('annulee',9,41);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',11,42);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',12,43);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('annulee',14,43);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',15,44);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',16,45);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',17,46);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',9,47);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('annulee',17,48);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',11,48);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',12,49);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',9,50);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',9,49);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',14,49);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',11,49);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('annulee',15,50);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',16,51);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',9,51);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',17,51);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',9,54);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',11,54);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',12,55);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',15,56);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',14,57);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',17,58);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',16,59);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('annulee',9,60);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',11,61);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',12,62);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',14,63);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',15,64);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',17,65);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',14,67);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',17,68);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',11,69);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',9,70);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',12,71);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',11,72);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',14,73);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',15,74);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',16,35);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',17,76);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',15,77);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',16,77);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',17,79);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',9,80);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',14,81);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',11,82);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',9,83);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('annulee',11,84);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',12,84);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',14,84);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',15,84);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',16,85);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('annulee',17,85);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',9,86);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',11,86);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',12,87);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',14,87);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',15,87);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',16,87);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',17,87);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',14,88);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',15,88);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('annulee',9,88);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',11,89);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',12,89);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('annulee',14,89);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',15,89);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',16,89);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',17,89);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',9,90);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('annulee',17,91);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',11,92);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',12,92);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',9,93);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',12,94);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',14,94);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',14,95);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',11,96);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('annulee',15,97);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',16,98);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',9,99);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',17,99);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',9,100);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',11,100);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',12,102);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',15,103);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',14,104);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',17,105);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',16,106);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('annulee',9,107);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',11,108);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',12,109);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',14,110);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',15,111);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',17,113);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',16,114);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',15,115);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',14,117);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',9,120);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',11,121);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',12,122);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',14,123);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',15,124);
insert into reservation_interne (etat_reservation,id_utilisateur,id_seance)
values ('reservee',16,125);



/***********pour archiver les séances passées *********/
select changementEtatReservationInterne();
select changementEtatReservationExterne();
select archivageSeance();