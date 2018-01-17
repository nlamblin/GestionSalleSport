--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- Name: f_(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_() RETURNS void
    LANGUAGE plpgsql
    AS $$


begin


end


$$;


ALTER FUNCTION public.f_() OWNER TO postgres;

--
-- Name: f_ajout_activite(text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_ajout_activite(vnom text, vtype text) RETURNS void
    LANGUAGE plpgsql
    AS $$


begin


    insert into "Activite" values (default,vnom,vtype);


end


$$;


ALTER FUNCTION public.f_ajout_activite(vnom text, vtype text) OWNER TO postgres;

--
-- Name: f_ajout_ami(integer, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_ajout_ami(iduser integer, emailami text) RETURNS void
    LANGUAGE plpgsql
    AS $$


declare


    idami int;


begin


    -- On récupère l'id de l'ami


    select "idUtilisateur" from "Utilisateur" where "email"=emailami into idami;


    -- Création du lien d'amitié


    insert into "Amis" VALUES (iduser,idami);


end


$$;


ALTER FUNCTION public.f_ajout_ami(iduser integer, emailami text) OWNER TO postgres;

--
-- Name: f_ajout_seance(integer, text, integer, date, text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_ajout_seance(idactivite integer, emailcoach text, vcapacite integer, vdate date, vstatut text, vtarif integer) RETURNS void
    LANGUAGE plpgsql
    AS $$


declare


    idcoach int;


begin


    SELECT "idUtilisateur" from "Utilisateur" where "email"=emailcoach into idcoach;


    insert into "Seance" values (default,idactivite,idcoach,vcapacite,vdate,vstatut,vtarif);


end


$$;


ALTER FUNCTION public.f_ajout_seance(idactivite integer, emailcoach text, vcapacite integer, vdate date, vstatut text, vtarif integer) OWNER TO postgres;

--
-- Name: f_archivage(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_archivage() RETURNS void
    LANGUAGE plpgsql
    AS $$


declare 


    idact int;


    idcoach int;


    dateseance date;


    iduser int;


    idseance int;


    typepaie int;


    idseancehist int;


begin


    -- On archive les séances


    FOR  idact,idcoach,dateseance,idseance in


        select "idActivite","idUtilisateur","dateSeance","idSeance" from "Seance" where "dateSeance"<CURRENT_DATE


    LOOP


        insert into "SeanceHist" values (default,idact,idcoach,dateseance) RETURNING "idSeanceHist" into idseancehist;


        -- Les réservations associées


        FOR iduser,typepaie IN


            select "idUtilisateur","idTypePaiement" from "Reservation" where "idSeance"=idseance


        LOOP


            insert into "ReservationHist" values (default,iduser,idseancehist,typepaie);


        END LOOP;


        -- On supprime ensuite les originaux (réservations supprimées grâce au trigger de suppression)


        delete from "Seance" where "idSeance"=idseance;


    END LOOP;


end


$$;


ALTER FUNCTION public.f_archivage() OWNER TO postgres;

--
-- Name: f_avert_abonnement(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_avert_abonnement() RETURNS void
    LANGUAGE plpgsql
    AS $$


begin


end


$$;


ALTER FUNCTION public.f_avert_abonnement() OWNER TO postgres;

--
-- Name: f_change_role(text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_change_role(v_email text, v_role text) RETURNS void
    LANGUAGE plpgsql
    AS $$


begin


    update "Utilisateur" set "role"=v_role where "email"=v_email;


end


$$;


ALTER FUNCTION public.f_change_role(v_email text, v_role text) OWNER TO postgres;

--
-- Name: f_connexion_user(text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_connexion_user(v_email text, v_password text) RETURNS boolean
    LANGUAGE plpgsql
    AS $$


begin


RETURN EXISTS (SELECT 1 FROM "Utilisateur" WHERE email = v_email AND password = v_password);


end


$$;


ALTER FUNCTION public.f_connexion_user(v_email text, v_password text) OWNER TO postgres;

--
-- Name: f_inscr_ami_seance(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_inscr_ami_seance() RETURNS void
    LANGUAGE plpgsql
    AS $$


begin


end


$$;


ALTER FUNCTION public.f_inscr_ami_seance() OWNER TO postgres;

--
-- Name: f_inscr_seance(integer, integer, boolean); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_inscr_seance(iduser integer, idseance integer, boolcoach boolean) RETURNS void
    LANGUAGE plpgsql
    AS $$


declare


    nbseances int;


    idcarte int;


    typeabo int;


begin


    IF (select "capacite" from "Seance" where "idSeance" = idseance) > 0 THEN


        -- L'utilisateur ne peut s'inscrire que s'il reste de la place


        IF (select "dateFin" from "Abonnement" natural join "Utilisateur" where "idUtilisateur"=iduser) >= CURRENT_DATE THEN


            -- l'utilisateur a un abonnement actif


            typeabo=2; -- type : cf. bdd


            IF boolcoach THEN


                -- TODO demande coach f_prendre_coach


            END IF;


        ELSE


            select "idCarte","nbSeanceRestante" from "Carte" natural join "Utilisateur" where "idUtilisateur"=iduser into idcarte,nbseances;


            IF nbseances > 0 THEN


                -- L'utilisateur a une carte active


                typeabo=3;


                -- crédite la carte


                update "Carte" set "nbSeanceRestante" = "nbSeanceRestante"-1 where "idCarte" = idcarte;


                IF (boolcoach AND nbseances > 1) THEN 


                    -- (> 1 car il y a maintenant une séance en moins sur la carte)


                    -- TODO demande coach f_prendre_coach


                    -- crédite la carte


                    update "Carte" set "nbSeanceRestante" = "nbSeanceRestante"-1 where "idCarte" = idcarte;


                END IF;


            ELSE


                -- doit payer le tarif


                typeabo=1;


            END IF;


        END IF;


        -- Gestion de la réservation


        -- TODO : check EtatReservation pcq table vide pour le moment


        insert into "Reservation" VALUES (DEFAULT,iduser,idseance,1,typeabo);


        -- Maj. de la capacité


        update "Seance" set "capacite"="capacite"-1 where "idSeance" = idseance;


    END IF;    


end


$$;


ALTER FUNCTION public.f_inscr_seance(iduser integer, idseance integer, boolcoach boolean) OWNER TO postgres;

--
-- Name: f_inscr_seance_client(text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_inscr_seance_client(v_email text, idseance integer) RETURNS void
    LANGUAGE plpgsql
    AS $$


declare


    iduser int;


begin


    -- On récupère l'id de l'utilisateur


    SELECT "idUtilisateur" from "Utilisateur" where "email"=v_email into iduser;


    -- On inscrit le client à la séance


    select f_inscr_seance(iduser, idseance, FALSE);


end


$$;


ALTER FUNCTION public.f_inscr_seance_client(v_email text, idseance integer) OWNER TO postgres;

--
-- Name: f_inscr_user(text, text, text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_inscr_user(v_nom text, v_prenom text, v_password text, v_email text) RETURNS void
    LANGUAGE plpgsql
    AS $$


declare carte_id int;


begin


-- Création de la carte associée


INSERT INTO "Carte" VALUES (DEFAULT,0) RETURNING "idCarte" INTO carte_id;


-- Création de l'utilisateur avec les variables entrées sur le site


INSERT INTO "Utilisateur" VALUES (


    DEFAULT,


    v_email,


    v_password,


    v_prenom,


    v_nom,


    CURRENT_DATE,


    'valide',


    'client',


    NULL,


    carte_id,


    FALSE


);


end


$$;


ALTER FUNCTION public.f_inscr_user(v_nom text, v_prenom text, v_password text, v_email text) OWNER TO postgres;

--
-- Name: f_modif_coach(integer, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_modif_coach(idseance integer, emailcoach text) RETURNS void
    LANGUAGE plpgsql
    AS $$


declare


    idcoach int;


begin


    SELECT "idUtilisateur" from "Utilisateur" where "email"=emailcoach into idcoach;


    update "Seance" set "idUtilisateur"=idcoach where "idSeance"=idseance;


end


$$;


ALTER FUNCTION public.f_modif_coach(idseance integer, emailcoach text) OWNER TO postgres;

--
-- Name: f_prendre_carte(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_prendre_carte(iduser integer, nbseances integer) RETURNS void
    LANGUAGE plpgsql
    AS $$


declare


    idcarte int;


begin


    -- On récupère la carte associée à l'utilisateur


    select "idCarte" from "Carte" natural join "Utilisateur" where "idUtilisateur"=iduser into idcarte;


    -- Ajout du nombre de séances correspondant à la carte


    UPDATE "Carte" SET "nbSeanceRestante" = nbseances WHERE "idCarte" = idcarte;


end


$$;


ALTER FUNCTION public.f_prendre_carte(iduser integer, nbseances integer) OWNER TO postgres;

--
-- Name: f_prendre_coach(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_prendre_coach() RETURNS void
    LANGUAGE plpgsql
    AS $$


begin


end


$$;


ALTER FUNCTION public.f_prendre_coach() OWNER TO postgres;

--
-- Name: f_prop_seances_complet(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_prop_seances_complet() RETURNS void
    LANGUAGE plpgsql
    AS $$


begin


end


$$;


ALTER FUNCTION public.f_prop_seances_complet() OWNER TO postgres;

--
-- Name: f_prop_seances_salle(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_prop_seances_salle() RETURNS void
    LANGUAGE plpgsql
    AS $$


begin


end


$$;


ALTER FUNCTION public.f_prop_seances_salle() OWNER TO postgres;

--
-- Name: f_set_internaute(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_set_internaute() RETURNS void
    LANGUAGE plpgsql
    AS $$


begin


set role u_internaute;


end


$$;


ALTER FUNCTION public.f_set_internaute() OWNER TO postgres;

--
-- Name: f_set_role(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_set_role(v_email text) RETURNS void
    LANGUAGE plpgsql
    AS $$


declare userrole text;


begin


userrole:=(select role from "Utilisateur" where "Utilisateur".email=v_email);


IF userrole = 'client' THEN


    set role u_client;


ELSIF userrole = 'admin' THEN


    set role u_admin;


ELSIF userrole = 'coach' THEN


    set role u_coach;


ELSIF userrole = 'employe' THEN


    set role u_employe;


ELSE


    set role u_internaute;


END IF;


end


$$;


ALTER FUNCTION public.f_set_role(v_email text) OWNER TO postgres;

--
-- Name: f_subscr_abonnement(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_subscr_abonnement(v_user integer, v_typeabo integer) RETURNS void
    LANGUAGE plpgsql
    AS $$


declare 


    abo_id int;


    enddate date;


begin


    if v_typeabo = 1 THEN


        enddate = CURRENT_DATE + (interval '1 month');


    elsif v_typeabo = 2 THEN


        enddate = CURRENT_DATE + (interval '1 year');


    elsif v_typeabo = 3 THEN


        enddate = CURRENT_DATE + (interval '1 week');


    elsif v_typeabo = 4 THEN


        enddate = CURRENT_DATE + (3 * interval '1 month');


    end if;


    -- Création de l'abonnement


    INSERT INTO "Abonnement" VALUES (DEFAULT,v_typeabo,CURRENT_DATE,enddate) RETURNING "idAbonnement" INTO abo_id;


    -- Ajout de l'abonnement au client


    UPDATE "Utilisateur" SET "idAbonnement" = abo_id WHERE "idUtilisateur" = v_user;


end


$$;


ALTER FUNCTION public.f_subscr_abonnement(v_user integer, v_typeabo integer) OWNER TO postgres;

--
-- Name: f_suppr_activite(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_suppr_activite(idactivite integer) RETURNS void
    LANGUAGE plpgsql
    AS $$


begin


    delete from "Activite" where "idActivite" = idactivite;


end


$$;


ALTER FUNCTION public.f_suppr_activite(idactivite integer) OWNER TO postgres;

--
-- Name: f_suppr_ami(integer, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_suppr_ami(iduser integer, emailami text) RETURNS void
    LANGUAGE plpgsql
    AS $$


declare


    idami int;


begin


    -- On récupère l'id de l'ami


    select "idUtilisateur" from "Utilisateur" where "email"=emailami into idami;


    -- On supprime les lignes concernées


    delete from "Amis" where ("idUtilisateur1"=iduser and "idUtilisateur2"=idami) or ("idUtilisateur1"=idami and "idUtilisateur2"=iduser);


end


$$;


ALTER FUNCTION public.f_suppr_ami(iduser integer, emailami text) OWNER TO postgres;

--
-- Name: f_suppr_seance(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_suppr_seance(idseance integer) RETURNS void
    LANGUAGE plpgsql
    AS $$


begin


    delete from "Seance" where "idSeance"=idseance;


end


$$;


ALTER FUNCTION public.f_suppr_seance(idseance integer) OWNER TO postgres;

--
-- Name: f_suppr_seance_client(text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_suppr_seance_client(v_email text, idseance integer) RETURNS void
    LANGUAGE plpgsql
    AS $$


declare


    iduser int;


begin


    -- On récupère l'id de l'utilisateur


    SELECT "idUtilisateur" from "Utilisateur" where "email"=v_email into iduser;


    -- On supprime la réservation de l'utilisateur et restore la capacité de la séance


    delete from "Reservation" where ("idUtilisateur"=iduser and "idSeance"=idseance);


    update "Seance" set "capacite"="capacite"+1 where "idSeance"=idseance;


end


$$;


ALTER FUNCTION public.f_suppr_seance_client(v_email text, idseance integer) OWNER TO postgres;

--
-- Name: f_suppr_user(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_suppr_user(v_email text) RETURNS void
    LANGUAGE plpgsql
    AS $$


declare 


    iduser int;


    dateseance date;


begin


    SELECT "idUtilisateur" from "Utilisateur" where "email"=v_email into iduser;


    -- On récupère la dernière date d'activité (séance à laquelle il a participé)


    select "dateSeance" from "Seance" 


        inner join "Reservation" on "Seance"."idSeance" = "Reservation"."idSeance" 


        where "Reservation"."idUtilisateur"=iduser 


        order by "dateSeance" desc limit 1 into dateseance;


    -- On vérifie que la séance date de plus d'un an


    if CURRENT_DATE >= (dateseance + (interval '1 year')) THEN


        delete from "Utilisateur" where "idUtilisateur" = iduser;


    end if;


end


$$;


ALTER FUNCTION public.f_suppr_user(v_email text) OWNER TO postgres;

--
-- Name: f_ti_avert_carte(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_ti_avert_carte() RETURNS trigger
    LANGUAGE plpgsql
    AS $$


declare


    nbseances int; 


begin


    select "nbSeanceRestante" from "Carte" where "idCarte"=new."idCarte" into nbseances;


    if nbseances = 0 THEN


        raise notice 'Attention : votre carte est épuisée.';


    end if;


    return new;


end


$$;


ALTER FUNCTION public.f_ti_avert_carte() OWNER TO postgres;

--
-- Name: f_voir_hist_seance(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_voir_hist_seance() RETURNS void
    LANGUAGE plpgsql
    AS $$


begin


end


$$;


ALTER FUNCTION public.f_voir_hist_seance() OWNER TO postgres;

--
-- Name: f_voir_plan_client(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_voir_plan_client() RETURNS void
    LANGUAGE plpgsql
    AS $$


begin


end


$$;


ALTER FUNCTION public.f_voir_plan_client() OWNER TO postgres;

--
-- Name: f_voir_plan_coach(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_voir_plan_coach() RETURNS void
    LANGUAGE plpgsql
    AS $$


begin


end


$$;


ALTER FUNCTION public.f_voir_plan_coach() OWNER TO postgres;

--
-- Name: f_voir_plan_seance(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION f_voir_plan_seance() RETURNS void
    LANGUAGE plpgsql
    AS $$


begin


end


$$;


ALTER FUNCTION public.f_voir_plan_seance() OWNER TO postgres;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: Abonnement; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "Abonnement" (
    "idAbonnement" integer NOT NULL,
    "idTypeAbonnement" integer NOT NULL,
    "dateDebut" date NOT NULL,
    "dateFin" date NOT NULL
);


ALTER TABLE public."Abonnement" OWNER TO postgres;

--
-- Name: AbonnementHist; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "AbonnementHist" (
    "idAbonnementHist" integer NOT NULL,
    "idUtilisateur" integer NOT NULL,
    type character varying(255) NOT NULL,
    "dateDebut" date NOT NULL,
    "dateFin" date NOT NULL,
    CONSTRAINT "AbonnementHist_type_check" CHECK (((type)::text = ANY ((ARRAY['annuel'::character varying, 'mensuel'::character varying, 'trismestriel'::character varying, 'semaine'::character varying])::text[])))
);


ALTER TABLE public."AbonnementHist" OWNER TO postgres;

--
-- Name: AbonnementHist_idAbonnementHist_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "AbonnementHist_idAbonnementHist_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."AbonnementHist_idAbonnementHist_seq" OWNER TO postgres;

--
-- Name: AbonnementHist_idAbonnementHist_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "AbonnementHist_idAbonnementHist_seq" OWNED BY "AbonnementHist"."idAbonnementHist";


--
-- Name: Abonnement_idAbonnement_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "Abonnement_idAbonnement_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."Abonnement_idAbonnement_seq" OWNER TO postgres;

--
-- Name: Abonnement_idAbonnement_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "Abonnement_idAbonnement_seq" OWNED BY "Abonnement"."idAbonnement";


--
-- Name: Activite; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "Activite" (
    "idActivite" integer NOT NULL,
    "nomActivite" character varying(50) NOT NULL,
    "typeActivite" character varying(50) NOT NULL
);


ALTER TABLE public."Activite" OWNER TO postgres;

--
-- Name: Activite_idActivite_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "Activite_idActivite_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."Activite_idActivite_seq" OWNER TO postgres;

--
-- Name: Activite_idActivite_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "Activite_idActivite_seq" OWNED BY "Activite"."idActivite";


--
-- Name: Amis; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "Amis" (
    "idUtilisateur1" integer NOT NULL,
    "idUtilisateur2" integer NOT NULL
);


ALTER TABLE public."Amis" OWNER TO postgres;

--
-- Name: Carte; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "Carte" (
    "idCarte" integer NOT NULL,
    "nbSeanceRestante" integer NOT NULL
);


ALTER TABLE public."Carte" OWNER TO postgres;

--
-- Name: Carte_idCarte_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "Carte_idCarte_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."Carte_idCarte_seq" OWNER TO postgres;

--
-- Name: Carte_idCarte_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "Carte_idCarte_seq" OWNED BY "Carte"."idCarte";


--
-- Name: EtatReservation; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "EtatReservation" (
    "idEtatReservation" integer NOT NULL,
    titre character varying(50) NOT NULL
);


ALTER TABLE public."EtatReservation" OWNER TO postgres;

--
-- Name: EtatReservation_idEtatReservation_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "EtatReservation_idEtatReservation_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."EtatReservation_idEtatReservation_seq" OWNER TO postgres;

--
-- Name: EtatReservation_idEtatReservation_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "EtatReservation_idEtatReservation_seq" OWNED BY "EtatReservation"."idEtatReservation";


--
-- Name: Reservation; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "Reservation" (
    "idReservation" integer NOT NULL,
    "idUtilisateur" integer NOT NULL,
    "idSeance" integer NOT NULL,
    "idEtatReservation" integer NOT NULL,
    "idTypePaiement" integer NOT NULL
);


ALTER TABLE public."Reservation" OWNER TO postgres;

--
-- Name: ReservationHist; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "ReservationHist" (
    "idReservationHist" integer NOT NULL,
    "idUtilisateur" integer NOT NULL,
    "idSeance" integer NOT NULL,
    "idTypePaiement" integer NOT NULL
);


ALTER TABLE public."ReservationHist" OWNER TO postgres;

--
-- Name: ReservationHist_idReservationHist_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "ReservationHist_idReservationHist_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."ReservationHist_idReservationHist_seq" OWNER TO postgres;

--
-- Name: ReservationHist_idReservationHist_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "ReservationHist_idReservationHist_seq" OWNED BY "ReservationHist"."idReservationHist";


--
-- Name: Reservation_idReservation_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "Reservation_idReservation_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."Reservation_idReservation_seq" OWNER TO postgres;

--
-- Name: Reservation_idReservation_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "Reservation_idReservation_seq" OWNED BY "Reservation"."idReservation";


--
-- Name: Seance; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "Seance" (
    "idSeance" integer NOT NULL,
    "idActivite" integer NOT NULL,
    "idUtilisateur" integer,
    capacite integer NOT NULL,
    "dateSeance" date NOT NULL,
    "statutSeance" character varying(255) NOT NULL,
    CONSTRAINT "Seance_statutSeance_check" CHECK ((("statutSeance")::text = ANY ((ARRAY['a venir'::character varying, 'en cours'::character varying, 'fin'::character varying])::text[])))
);


ALTER TABLE public."Seance" OWNER TO postgres;

--
-- Name: SeanceHist; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "SeanceHist" (
    "idSeanceHist" integer NOT NULL,
    "idActivite" integer NOT NULL,
    "idUtilisateur" integer NOT NULL,
    "dateSeance" date NOT NULL
);


ALTER TABLE public."SeanceHist" OWNER TO postgres;

--
-- Name: SeanceHist_idSeanceHist_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "SeanceHist_idSeanceHist_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."SeanceHist_idSeanceHist_seq" OWNER TO postgres;

--
-- Name: SeanceHist_idSeanceHist_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "SeanceHist_idSeanceHist_seq" OWNED BY "SeanceHist"."idSeanceHist";


--
-- Name: Seance_idSeance_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "Seance_idSeance_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."Seance_idSeance_seq" OWNER TO postgres;

--
-- Name: Seance_idSeance_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "Seance_idSeance_seq" OWNED BY "Seance"."idSeance";


--
-- Name: TypeAbonnement; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "TypeAbonnement" (
    "idTypeAbonnement" integer NOT NULL,
    intitule character varying(255) NOT NULL,
    tarif integer NOT NULL
);


ALTER TABLE public."TypeAbonnement" OWNER TO postgres;

--
-- Name: TypeAbonnement_idTypeAbonnement_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "TypeAbonnement_idTypeAbonnement_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."TypeAbonnement_idTypeAbonnement_seq" OWNER TO postgres;

--
-- Name: TypeAbonnement_idTypeAbonnement_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "TypeAbonnement_idTypeAbonnement_seq" OWNED BY "TypeAbonnement"."idTypeAbonnement";


--
-- Name: TypePaiement; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "TypePaiement" (
    "idTypePaiement" integer NOT NULL,
    titre character varying(50) NOT NULL
);


ALTER TABLE public."TypePaiement" OWNER TO postgres;

--
-- Name: TypePaiement_idTypePaiement_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "TypePaiement_idTypePaiement_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."TypePaiement_idTypePaiement_seq" OWNER TO postgres;

--
-- Name: TypePaiement_idTypePaiement_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "TypePaiement_idTypePaiement_seq" OWNED BY "TypePaiement"."idTypePaiement";


--
-- Name: Utilisateur; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "Utilisateur" (
    "idUtilisateur" integer NOT NULL,
    email character varying(50) NOT NULL,
    password character varying(100) NOT NULL,
    prenom character varying(50) NOT NULL,
    nom character varying(50) NOT NULL,
    "dateInscription" date NOT NULL,
    statut character varying(255) NOT NULL,
    role character varying(255) NOT NULL,
    "idAbonnement" integer,
    "idCarte" integer,
    relance boolean NOT NULL,
    remember_token character varying(100),
    CONSTRAINT "Utilisateur_role_check" CHECK (((role)::text = ANY ((ARRAY['coach'::character varying, 'client'::character varying, 'employe'::character varying, 'admin'::character varying])::text[]))),
    CONSTRAINT "Utilisateur_statut_check" CHECK (((statut)::text = ANY ((ARRAY['valide'::character varying, 'en attente'::character varying, 'expire'::character varying])::text[])))
);


ALTER TABLE public."Utilisateur" OWNER TO postgres;

--
-- Name: Utilisateur_idUtilisateur_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "Utilisateur_idUtilisateur_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."Utilisateur_idUtilisateur_seq" OWNER TO postgres;

--
-- Name: Utilisateur_idUtilisateur_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "Utilisateur_idUtilisateur_seq" OWNED BY "Utilisateur"."idUtilisateur";


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE migrations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.migrations_id_seq OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE migrations_id_seq OWNED BY migrations.id;


--
-- Name: idAbonnement; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Abonnement" ALTER COLUMN "idAbonnement" SET DEFAULT nextval('"Abonnement_idAbonnement_seq"'::regclass);


--
-- Name: idAbonnementHist; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "AbonnementHist" ALTER COLUMN "idAbonnementHist" SET DEFAULT nextval('"AbonnementHist_idAbonnementHist_seq"'::regclass);


--
-- Name: idActivite; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Activite" ALTER COLUMN "idActivite" SET DEFAULT nextval('"Activite_idActivite_seq"'::regclass);


--
-- Name: idCarte; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Carte" ALTER COLUMN "idCarte" SET DEFAULT nextval('"Carte_idCarte_seq"'::regclass);


--
-- Name: idEtatReservation; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "EtatReservation" ALTER COLUMN "idEtatReservation" SET DEFAULT nextval('"EtatReservation_idEtatReservation_seq"'::regclass);


--
-- Name: idReservation; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Reservation" ALTER COLUMN "idReservation" SET DEFAULT nextval('"Reservation_idReservation_seq"'::regclass);


--
-- Name: idReservationHist; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "ReservationHist" ALTER COLUMN "idReservationHist" SET DEFAULT nextval('"ReservationHist_idReservationHist_seq"'::regclass);


--
-- Name: idSeance; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Seance" ALTER COLUMN "idSeance" SET DEFAULT nextval('"Seance_idSeance_seq"'::regclass);


--
-- Name: idSeanceHist; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "SeanceHist" ALTER COLUMN "idSeanceHist" SET DEFAULT nextval('"SeanceHist_idSeanceHist_seq"'::regclass);


--
-- Name: idTypeAbonnement; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "TypeAbonnement" ALTER COLUMN "idTypeAbonnement" SET DEFAULT nextval('"TypeAbonnement_idTypeAbonnement_seq"'::regclass);


--
-- Name: idTypePaiement; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "TypePaiement" ALTER COLUMN "idTypePaiement" SET DEFAULT nextval('"TypePaiement_idTypePaiement_seq"'::regclass);


--
-- Name: idUtilisateur; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Utilisateur" ALTER COLUMN "idUtilisateur" SET DEFAULT nextval('"Utilisateur_idUtilisateur_seq"'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY migrations ALTER COLUMN id SET DEFAULT nextval('migrations_id_seq'::regclass);


--
-- Data for Name: Abonnement; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO "Abonnement" VALUES (1, 5, '2018-01-13', '2038-01-13');
INSERT INTO "Abonnement" VALUES (2, 5, '2018-01-13', '2038-01-13');
INSERT INTO "Abonnement" VALUES (3, 5, '2018-01-13', '2038-01-13');
INSERT INTO "Abonnement" VALUES (4, 2, '2018-01-13', '2019-01-13');
INSERT INTO "Abonnement" VALUES (5, 2, '2018-01-13', '2019-01-13');
INSERT INTO "Abonnement" VALUES (6, 2, '2018-01-13', '2019-01-13');


--
-- Data for Name: AbonnementHist; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Name: AbonnementHist_idAbonnementHist_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('"AbonnementHist_idAbonnementHist_seq"', 1, false);


--
-- Name: Abonnement_idAbonnement_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('"Abonnement_idAbonnement_seq"', 6, true);


--
-- Data for Name: Activite; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO "Activite" VALUES (1, 'Zumba', 'collective');
INSERT INTO "Activite" VALUES (2, 'Cours debutant', 'individuel');
INSERT INTO "Activite" VALUES (3, 'Cardio renforce', 'collective');
INSERT INTO "Activite" VALUES (4, 'Abdominaux', 'collective');


--
-- Name: Activite_idActivite_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('"Activite_idActivite_seq"', 4, true);


--
-- Data for Name: Amis; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: Carte; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO "Carte" VALUES (1, 99999);
INSERT INTO "Carte" VALUES (2, 99999);
INSERT INTO "Carte" VALUES (3, 99999);
INSERT INTO "Carte" VALUES (4, 0);
INSERT INTO "Carte" VALUES (5, 0);
INSERT INTO "Carte" VALUES (6, 0);


--
-- Name: Carte_idCarte_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('"Carte_idCarte_seq"', 6, true);


--
-- Data for Name: EtatReservation; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO "EtatReservation" VALUES (1, 'en attente');
INSERT INTO "EtatReservation" VALUES (2, 'valide');
INSERT INTO "EtatReservation" VALUES (3, 'refuse');


--
-- Name: EtatReservation_idEtatReservation_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('"EtatReservation_idEtatReservation_seq"', 3, true);


--
-- Data for Name: Reservation; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO "Reservation" VALUES (3, 4, 1, 1, 2);
INSERT INTO "Reservation" VALUES (4, 5, 1, 1, 2);
INSERT INTO "Reservation" VALUES (5, 6, 1, 1, 2);


--
-- Data for Name: ReservationHist; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Name: ReservationHist_idReservationHist_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('"ReservationHist_idReservationHist_seq"', 1, false);


--
-- Name: Reservation_idReservation_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('"Reservation_idReservation_seq"', 6, true);


--
-- Data for Name: Seance; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO "Seance" VALUES (1, 4, 1, 3, '2018-01-24', 'a venir');
INSERT INTO "Seance" VALUES (2, 4, 1, 3, '2018-01-17', 'a venir');
INSERT INTO "Seance" VALUES (3, 4, 1, 4, '2018-01-25', 'a venir');
INSERT INTO "Seance" VALUES (4, 4, 1, 3, '2018-01-22', 'a venir');


--
-- Data for Name: SeanceHist; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Name: SeanceHist_idSeanceHist_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('"SeanceHist_idSeanceHist_seq"', 1, false);


--
-- Name: Seance_idSeance_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('"Seance_idSeance_seq"', 4, true);


--
-- Data for Name: TypeAbonnement; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO "TypeAbonnement" VALUES (1, 'mensuel', 35);
INSERT INTO "TypeAbonnement" VALUES (2, 'annuel', 350);
INSERT INTO "TypeAbonnement" VALUES (3, 'hebdomadaire', 12);
INSERT INTO "TypeAbonnement" VALUES (4, 'trimestriel', 95);
INSERT INTO "TypeAbonnement" VALUES (5, 'employe', 0);


--
-- Name: TypeAbonnement_idTypeAbonnement_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('"TypeAbonnement_idTypeAbonnement_seq"', 5, true);


--
-- Data for Name: TypePaiement; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO "TypePaiement" VALUES (1, 'liquide');
INSERT INTO "TypePaiement" VALUES (2, 'abonnement');
INSERT INTO "TypePaiement" VALUES (3, 'carte');


--
-- Name: TypePaiement_idTypePaiement_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('"TypePaiement_idTypePaiement_seq"', 3, true);


--
-- Data for Name: Utilisateur; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO "Utilisateur" VALUES (5, 'client2@miagiquefit.fr', '$2y$10$bNPFWq5F3Cl4WaE/KZ2U8uzUfHtyIC1FAkORnWLLDLIoDLokiOTEW', 'Frederic', 'Chaussure', '2018-01-13', 'en attente', 'client', 5, 5, true, NULL);
INSERT INTO "Utilisateur" VALUES (6, 'client3@miagiquefit.fr', '$2y$10$iGZ5SuRzI2MyyqvikICUge2iOvDh/66mklmOFHNYC1QEei3onqWym', 'Eric', 'Saumon', '2018-01-13', 'en attente', 'client', 6, 6, true, NULL);
INSERT INTO "Utilisateur" VALUES (3, 'employe@miagiquefit.fr', '$2y$10$YjrFm47HWoeQfq150BQIrOdmLDICa/4tcC1fMakAly8QvU53wntsS', 'Eric', 'Velo', '2018-01-13', 'valide', 'employe', 3, 3, true, 'zrmBrDJPbKOuzR6eXKSV0Z3aymsssWWNNqXeTAk7TQn2yOTiz5Q6b5kNxFTV');
INSERT INTO "Utilisateur" VALUES (1, 'coach@miagiquefit.fr', '$2y$10$eCIhw7ZGetYPB/8s81lPT.TDuBQh1qJMMcMY8rcvrgvBo0Ss88teO', 'Jean', 'Dupond', '2018-01-13', 'valide', 'coach', 1, 1, true, 'zUHDffve3oozqWuKumFt2r8dwsNl71wOzudLrPD4jJ8Iwcjvwu0ciNkKXK9F');
INSERT INTO "Utilisateur" VALUES (4, 'client@miagiquefit.fr', '$2y$10$QmVrCvxInaUC1fq.gIfHI.vd0iDfCEuPnyr1.RbJaZmN/vImdWgIK', 'Francis', 'Table', '2018-01-13', 'valide', 'client', 4, 4, true, 'eBkilYwV3izNRD2MclW2Kpa6ZDMdwQ73W7flgxY9OrCEMFps3ARMZOY4QY3p');
INSERT INTO "Utilisateur" VALUES (2, 'admin@miagiquefit.fr', '$2y$10$ciAoM/aUQtOo8NLYjWmNK.3FefTj1a9hTJDa28t9eTjQQDffCmMNS', 'Marc', 'Sand', '2018-01-13', 'valide', 'admin', 2, 2, true, 'a7fEyu1jBC3kLCrSPDzXZn586lW5zZbakv8tUlBo73r0o5Yva8Eh4uELClid');


--
-- Name: Utilisateur_idUtilisateur_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('"Utilisateur_idUtilisateur_seq"', 6, true);


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO migrations VALUES (762, '2017_12_08_140948_abonnement_type', 1);
INSERT INTO migrations VALUES (763, '2017_12_08_140949_create_abonnement_table', 1);
INSERT INTO migrations VALUES (764, '2017_12_08_141001_create_carte_table', 1);
INSERT INTO migrations VALUES (765, '2017_12_08_141002_create_user_table', 1);
INSERT INTO migrations VALUES (766, '2017_12_08_143814_create_abonnement_hist_table', 1);
INSERT INTO migrations VALUES (767, '2017_12_08_143836_create_amis_table', 1);
INSERT INTO migrations VALUES (768, '2017_12_08_143852_create_activite_table', 1);
INSERT INTO migrations VALUES (769, '2017_12_08_143905_create_type_paiement_table', 1);
INSERT INTO migrations VALUES (770, '2017_12_08_143920_create_etatReservation_table', 1);
INSERT INTO migrations VALUES (771, '2017_12_08_144331_create_seance_table', 1);
INSERT INTO migrations VALUES (772, '2017_12_08_144347_create_seance_hist_table', 1);
INSERT INTO migrations VALUES (773, '2017_12_08_144443_create_reservation_table', 1);
INSERT INTO migrations VALUES (774, '2017_12_08_150646_create_reservation_hist_table', 1);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('migrations_id_seq', 774, true);


--
-- Name: AbonnementHist_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "AbonnementHist"
    ADD CONSTRAINT "AbonnementHist_pkey" PRIMARY KEY ("idAbonnementHist");


--
-- Name: Abonnement_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Abonnement"
    ADD CONSTRAINT "Abonnement_pkey" PRIMARY KEY ("idAbonnement");


--
-- Name: Activite_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Activite"
    ADD CONSTRAINT "Activite_pkey" PRIMARY KEY ("idActivite");


--
-- Name: Carte_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Carte"
    ADD CONSTRAINT "Carte_pkey" PRIMARY KEY ("idCarte");


--
-- Name: EtatReservation_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "EtatReservation"
    ADD CONSTRAINT "EtatReservation_pkey" PRIMARY KEY ("idEtatReservation");


--
-- Name: ReservationHist_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "ReservationHist"
    ADD CONSTRAINT "ReservationHist_pkey" PRIMARY KEY ("idReservationHist");


--
-- Name: Reservation_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Reservation"
    ADD CONSTRAINT "Reservation_pkey" PRIMARY KEY ("idReservation");


--
-- Name: SeanceHist_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "SeanceHist"
    ADD CONSTRAINT "SeanceHist_pkey" PRIMARY KEY ("idSeanceHist");


--
-- Name: Seance_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Seance"
    ADD CONSTRAINT "Seance_pkey" PRIMARY KEY ("idSeance");


--
-- Name: TypeAbonnement_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "TypeAbonnement"
    ADD CONSTRAINT "TypeAbonnement_pkey" PRIMARY KEY ("idTypeAbonnement");


--
-- Name: TypePaiement_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "TypePaiement"
    ADD CONSTRAINT "TypePaiement_pkey" PRIMARY KEY ("idTypePaiement");


--
-- Name: Utilisateur_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Utilisateur"
    ADD CONSTRAINT "Utilisateur_pkey" PRIMARY KEY ("idUtilisateur");


--
-- Name: migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: utilisateur_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Utilisateur"
    ADD CONSTRAINT utilisateur_email_unique UNIQUE (email);


--
-- Name: abonnement_idtypeabonnement_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Abonnement"
    ADD CONSTRAINT abonnement_idtypeabonnement_foreign FOREIGN KEY ("idTypeAbonnement") REFERENCES "TypeAbonnement"("idTypeAbonnement") ON DELETE CASCADE;


--
-- Name: abonnementhist_idutilisateur_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "AbonnementHist"
    ADD CONSTRAINT abonnementhist_idutilisateur_foreign FOREIGN KEY ("idUtilisateur") REFERENCES "Utilisateur"("idUtilisateur") ON DELETE CASCADE;


--
-- Name: amis_idutilisateur1_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Amis"
    ADD CONSTRAINT amis_idutilisateur1_foreign FOREIGN KEY ("idUtilisateur1") REFERENCES "Utilisateur"("idUtilisateur") ON DELETE CASCADE;


--
-- Name: amis_idutilisateur2_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Amis"
    ADD CONSTRAINT amis_idutilisateur2_foreign FOREIGN KEY ("idUtilisateur2") REFERENCES "Utilisateur"("idUtilisateur") ON DELETE CASCADE;


--
-- Name: reservation_idetatreservation_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Reservation"
    ADD CONSTRAINT reservation_idetatreservation_foreign FOREIGN KEY ("idEtatReservation") REFERENCES "EtatReservation"("idEtatReservation") ON DELETE CASCADE;


--
-- Name: reservation_idseance_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Reservation"
    ADD CONSTRAINT reservation_idseance_foreign FOREIGN KEY ("idSeance") REFERENCES "Seance"("idSeance") ON DELETE CASCADE;


--
-- Name: reservation_idtypepaiement_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Reservation"
    ADD CONSTRAINT reservation_idtypepaiement_foreign FOREIGN KEY ("idTypePaiement") REFERENCES "TypePaiement"("idTypePaiement") ON DELETE CASCADE;


--
-- Name: reservation_idutilisateur_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Reservation"
    ADD CONSTRAINT reservation_idutilisateur_foreign FOREIGN KEY ("idUtilisateur") REFERENCES "Utilisateur"("idUtilisateur") ON DELETE CASCADE;


--
-- Name: reservationhist_idseance_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "ReservationHist"
    ADD CONSTRAINT reservationhist_idseance_foreign FOREIGN KEY ("idSeance") REFERENCES "Seance"("idSeance") ON DELETE CASCADE;


--
-- Name: reservationhist_idtypepaiement_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "ReservationHist"
    ADD CONSTRAINT reservationhist_idtypepaiement_foreign FOREIGN KEY ("idTypePaiement") REFERENCES "TypePaiement"("idTypePaiement") ON DELETE CASCADE;


--
-- Name: reservationhist_idutilisateur_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "ReservationHist"
    ADD CONSTRAINT reservationhist_idutilisateur_foreign FOREIGN KEY ("idUtilisateur") REFERENCES "Utilisateur"("idUtilisateur") ON DELETE CASCADE;


--
-- Name: seance_idactivite_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Seance"
    ADD CONSTRAINT seance_idactivite_foreign FOREIGN KEY ("idActivite") REFERENCES "Activite"("idActivite") ON DELETE CASCADE;


--
-- Name: seance_idutilisateur_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Seance"
    ADD CONSTRAINT seance_idutilisateur_foreign FOREIGN KEY ("idUtilisateur") REFERENCES "Utilisateur"("idUtilisateur") ON DELETE CASCADE;


--
-- Name: seancehist_idactivite_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "SeanceHist"
    ADD CONSTRAINT seancehist_idactivite_foreign FOREIGN KEY ("idActivite") REFERENCES "Activite"("idActivite") ON DELETE CASCADE;


--
-- Name: seancehist_idutilisateur_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "SeanceHist"
    ADD CONSTRAINT seancehist_idutilisateur_foreign FOREIGN KEY ("idUtilisateur") REFERENCES "Utilisateur"("idUtilisateur") ON DELETE CASCADE;


--
-- Name: utilisateur_idabonnement_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Utilisateur"
    ADD CONSTRAINT utilisateur_idabonnement_foreign FOREIGN KEY ("idAbonnement") REFERENCES "Abonnement"("idAbonnement") ON DELETE CASCADE;


--
-- Name: utilisateur_idcarte_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Utilisateur"
    ADD CONSTRAINT utilisateur_idcarte_foreign FOREIGN KEY ("idCarte") REFERENCES "Carte"("idCarte") ON DELETE CASCADE;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT USAGE ON SCHEMA public TO PUBLIC;


--
-- Name: migrations_id_seq; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON SEQUENCE migrations_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE migrations_id_seq FROM postgres;
GRANT ALL ON SEQUENCE migrations_id_seq TO postgres;
GRANT SELECT,USAGE ON SEQUENCE migrations_id_seq TO PUBLIC;


--
-- PostgreSQL database dump complete
--


CREATE SEQUENCE "vueReservationExterne_idReservationExterne_seq"
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;

CREATE TABLE "vueReservationExterne"
(
  "idReservationExterne" integer NOT NULL DEFAULT nextval('"vueReservationExterne_idReservationExterne_seq"'::regclass),
  "idUtilisateur" integer NOT NULL,
  "idSeance" integer NOT NULL,
  CONSTRAINT "vueReservationExterne_pkey" PRIMARY KEY ("idReservationExterne"),
  CONSTRAINT vueReservationExterne_idseance_foreign FOREIGN KEY ("idSeance")
      REFERENCES "Seance" ("idSeance") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE
);
