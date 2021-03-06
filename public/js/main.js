$(document).ready(function () {

    // configuration de ajax pour pouvoir utiliser POST
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // affichage / ou non de délai de relance
    if($('#demande_relance').is(':checked')) {
        $('#divDelai').show();
    }
    else {
        $('#divDelai').hide();
    }

    $('#demande_relance').change(function()  {
        if($(this).is(":checked")) {
            $('#divDelai').show('500');
        }
        else {
            $('#divDelai').hide('500');
        }
    });

     // affichage ou non du nombre de places de la séances en fonction du type de la seance
    $('#type_seance').change(function()  {
        let type = $('#type_seance').val();

        if(type ==='collective'){
            $('#divPlacesSeances').show('500');
        }
        else{
            $('#divPlacesSeances').hide('500');
        }
    });

    //Permet de verifier la date du formulaire de création de séance
    $("#date_seance").datetimepicker({
        format: "DD/MM/YYYY",
        minDate : moment().add(1, 'd').toDate(),
        maxDate : moment().add(1, 'Y').toDate()
    });

    // Permet de n'utiliser que les heures entières
    $('#heure_seance').datetimepicker({
        format: 'HH:00',
        enabledHours: [8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20]
    });

    // appel ajax pour récupérer les seances disponibles en fonction des activités
    $('#select_activite').change(function() {
        // on supprime les seances deja affichées
        $('.listeSeances').remove();

        // si ce n'est pas l'option par defaut
        if($(this).val() !== 'default') {
            $.ajax({
                method : 'GET',
                url : 'listeSeances',
                data : {
                    id_activite : $(this).val()
                },
                xhrFields: { withCredentials: true },
                crossDomain : true
            }).done(function (data) {
                // on affiche les nouvelles seances
                $('.rowSelectActivite').after(data);
            });
        }
    });

    // appel ajax pour récupérer les reservation d'un client
    $('#select_client_annulation').change(function() {
        // on supprime les seances avec les reservations à venir
        $('.listeSeancesVenirClient').remove();

        // si ce n'est pas l'option par defaut
        if($(this).val() !== 'default') {
            $.ajax({
                method : 'GET',
                url : 'seanceVenirClient',
                data : {
                    id_client : $(this).val()
                },
                xhrFields: { withCredentials: true },
                crossDomain : true
            }).done(function (data) {
                // on affiche les nouvelles seances
                $('.rowSelectClientAnnulation').after(data);
            });
        }
    });

    // appel ajax pour récupérer les séances d'un coach
    $('#select_coach').change(function() {
        // on supprime les seances avec les reservations à venir
        $('.listeSeancesCoach').remove();

        // si ce n'est pas l'option par defaut
        if($(this).val() !== 'default') {
            $.ajax({
                method : 'GET',
                url : 'seanceVenirCoach',
                data : {
                    id_coach : $(this).val()
                },
                xhrFields: { withCredentials: true },
                crossDomain : true
            }).done(function (data) {
                // on affiche les nouvelles seances
                $('.rowSelectCoach').after(data);
            });
        }
    });

    //appel ajax dans le cas de la reservation par un empployé
    $('#select_client_reservation').change(function() { 
        //Si le client change   
        console.log('changement');
        $('.listeSeancesReservationClient').remove();
        $('#select_activite_reservation_client').selectedIndex = 0;
    });

    // appel ajax pour récupérer les seances disponibles en fonction des activités pour les reservations clients
    $('#select_activite_reservation_client').change(function() {
        // on supprime les seances deja affichées
        $('.listeSeancesReservationClient').remove();

        // si ce n'est pas l'option par defaut
        if($(this).val() !== 'default') {
            $.ajax({
                method : 'GET',
                url : 'listeSeancesReservationClient',
                data : {
                    id_activite : $(this).val()
                },
                xhrFields: { withCredentials: true },
                crossDomain : true
            }).done(function (data) {
                // on affiche les nouvelles seances
                $('.row_select_activite_reservation_client').after(data);
            });
        }
    });

    $(document).on('click', '.bouton-annuler-reservation', function () {
        $.ajax({
            method  : 'POST',
            url     : 'annulerReservation',
            data    : {
                id_reservation : $(this).data('reservation')
            },
            xhrFields: { withCredentials: true },
            crossDomain : true
        }).done(function(data) {
            alert(data);
            window.location.reload();
        });
    });

    // appel ajax pour choisir abonnement ou carte
    $('#achat-abonnement').on('click', function() {
        let val = $('#choix-type-abo').val();

        if(val !== 'default') {
            $.ajax({
                method : 'PUT',
                url    : 'client/prendreCarteAbonnement',
                data   : {
                   typeAbo : val
                },
                xhrFields: { withCredentials: true },
                crossDomain : true
            })
            .done(function(data) {
                alert(data);
                window.location.reload();
            });
        }
    });

    $('.bouton-reservation-salle-externe').on('click', function() {
        $.ajax({
            method : 'PUT',
            url : 'effectuerReservationSalleExterne',
            data: {
                idSeance : $(this).data('idseance')
            },
            xhrFields: { withCredentials: true },
            crossDomain : true
        })
        .done(function(data) {
            alert(data);
            window.location.reload();
        });

    });

});



//**********************************************************************
//*********************** MODAL POUR LA RESERVATION ********************
//**********************************************************************

// fonction appelée à l'ouverture du modal pour la réservation
$(document).on('show.bs.modal', '#reservationModal', function (e) {

    $('#lien-recommandations').hide();
    $('.warning-places-restantes').hide();

    let typeSeance = $(e.relatedTarget).data('typeseance')
        , placesRestantes = $(e.relatedTarget).data('placesrestantes')
        , idSeance = $(e.relatedTarget).data('seance')
        , avecCoach = $(e.relatedTarget).data('aveccoach')
        , personnesAAjouter = [];

    if(placesRestantes === 0) {
        $('#lien-recommandations').show();
        $('#lien-recommandations').attr("href", $(e.relatedTarget).data('hrefrecommandations'));
        $('#reservation-seance').attr('disabled', true);

        // on désactive tous les champs
        $('.reservationForm :input').attr('disabled', true);
    }
    else {
        $('#reservation-seance').attr('disabled', false);
        $('.reservationForm :input').attr('disabled', false);
        $('.message-recommandations').hide();

        if(placesRestantes === 1) {
            $('.warning-places-restantes').show();
            $('#ajout-personne').attr('disabled', true);
            $('.bouton-ajout-personne').attr('disabled', true);
        }
        else {
            $('.warning-places-restantes').hide();
            $('#ajout-personne').removeAttr('disabled');
            $('.bouton-ajout-personne').removeAttr('disabled');
        }
    }

    $('.bouton-ajout-personne').on('click', function () {

        let idUtilisateur = $('#ajout-personne').val();

        if(idUtilisateur !== 'default') {
            // Si la personne n'a pas déjà été ajoutée
            if (personnesAAjouter[idUtilisateur] === undefined) {

                let optionSelected = $('#ajout-personne option:selected')[0]
                    , nomUtilisateur = $(optionSelected).data('name')
                    , prenomUtilisateur = $(optionSelected).data('prenom')
                    , emailUtilisateur = $(optionSelected).data('email');

                personnesAAjouter[idUtilisateur] = [];
                personnesAAjouter[idUtilisateur]['nom'] = nomUtilisateur;
                personnesAAjouter[idUtilisateur]['prenom'] = prenomUtilisateur;
                personnesAAjouter[idUtilisateur]['email'] = emailUtilisateur;

                // ajout de la personne dans la liste
                $('.listePersonneAAjouter').append(
                    '<li class="list-group-item" id="li-utilisateur-' + idUtilisateur + '">' +
                    '<span data-idUtilisateur="' + idUtilisateur + '" class="badge removePersonne">&times;</span>' +
                    prenomUtilisateur + ' ' + nomUtilisateur + ' &lt' + emailUtilisateur + '&gt ' +
                    '</li>');
            }
        }

        // quand on supprime une personne
        $('.removePersonne').on('click', function () {
            idUtilisateur = $(this).data('idutilisateur');
            delete personnesAAjouter[idUtilisateur];
            $('li').remove('#li-utilisateur-' + idUtilisateur);
        });
    });

    choixCoach();
    typeSeanceEtCoach(typeSeance, avecCoach, idSeance);
    reservationSeance(idSeance, personnesAAjouter, null);
});

// fonction appelée à la fermeture du modal pour la réservation
$(document).on('hidden.bs.modal', '#reservationModal', function(e) {
    // on reset le formulaire
    $('.reservationForm').trigger('reset');
    // on affiche le choix d'un coach (partie intégrante du reset)
    $('.div-choix-coach').show();
    // on supprime la liste des personnes à ajouter
    $('.listePersonneAAjouter').children().remove();
});



// fonction appelée à l'ouverture du modal pour la réservation par un employé
$(document).on('show.bs.modal', '#gestionReservationClientModal', function (e) {
    let typeSeance = $(e.relatedTarget).data('typeseance')
        , idSeance = $(e.relatedTarget).data('seance')
        , avecCoach = $(e.relatedTarget).data('aveccoach');

    let idClient =$('#select_client_reservation').val();
       
    choixCoach();
    typeSeanceEtCoach(typeSeance, avecCoach, idSeance);
    reservationSeance(idSeance, null, idClient);
});

// fonction appelée à la fermeture du modal pour la réservation d'une seance par un employé
$(document).on('hidden.bs.modal', '#gestionReservationClientModal', function(e) {
    // on reset le formulaire
    $('.reservationForm').trigger('reset');
    // on affiche le choix d'un coach (partie intégrante du reset)
    $('.div-choix-coach').show();
});






//**********************************************************************
//*********************** FONCTIONS ************************************
//**********************************************************************

// Récupère seulement les id des personnes à ajouter
let idPersonnesAAjouter = function (personnesAAjouter) {
    let idPersonnesAAjouter = [];

    $(personnesAAjouter).each(function(i, item) {
        if(item !== undefined) {
            idPersonnesAAjouter.push(i);
        }
    });

    return idPersonnesAAjouter;
};

// affiche ou non le choix d'un coach et recupere les coachs disponibles
function typeSeanceEtCoach(typeSeance, avecCoach, idSeance) {
    if(typeSeance === 'collective') {
        $('.div-choix-coach').hide();
        $('.div-select-coach').hide();
        $('.div-ajout-personne').show();

        $.ajax({
            method : 'GET',
            url : 'utilisateursValidesEtNonInscrit',
            data : {
                'id_seance': idSeance
            },
            xhrFields: {withCredentials: true},
            crossDomain: true
        })
        .done(function(data) {
            $.each(data, function(i, item) {
                $('#default-value-ajout-personnes').after("<option value='" + item.id_utilisateur + "' data-name='" + item.nom_utilisateur + "' data-prenom='" + item.prenom_utilisateur + "' data-email='" + item.email + "'>" + item.prenom_utilisateur + " " + item.nom_utilisateur + " &lt" + item.email + "&gt</option>");
            });
        });
    }
    else if(typeSeance === 'individuelle') {
        $('.div-ajout-personne').hide();

        if(!avecCoach) {
            $('.div-choix-coach').hide();
            $('.div-select-coach').hide();
            $('#choix-coach').attr('checked', false)
        }
        else {
            $.ajax({
                method: 'GET',
                url: 'coachsDisponibles',
                data: {
                    'idSeance': idSeance
                },
                xhrFields: {withCredentials: true},
                crossDomain: true
            }).done(function (data) {
                $.each(data, function (i, item) {
                    let option = new Option(item.prenom_utilisateur + ' ' + item.nom_utilisateur, item.id_utilisateur);
                    $('#select-coach').append(option);
                });
            });
        }
    }
}

// fonction qui enregistre une seance
function reservationSeance(idSeance, personnesAAjouter, idUtilisateur) {
    $('#reservation-seance').on('click', function() {

        let idCoach = null,
            idsPersonnes = null;

        if(personnesAAjouter !== null) {
            idsPersonnes = idPersonnesAAjouter(personnesAAjouter);
        }

        if ($('#choix-coach').is(':checked')) idCoach = $('#select-coach').val();

        $.ajax({
            method  : 'PUT',
            url     : $(this).data('url'),
            data    : {
                'idSeance'          : idSeance,
                'personnesAAjouter' : idsPersonnes,
                'idCoach'           : idCoach,
                'idUtilisateur'     : idUtilisateur
            },
            xhrFields: { withCredentials: true },
            crossDomain : true
        }).done(function (data) {
            // fermeture du modal
            $('#reservationModal').modal('hide');
            // affichage du message concernant la reservation
            alert(data);
            // rechargement de la page
            window.location.reload();
        });
    });
}

// affichage / ou non du choix du coach
function choixCoach() {
    $('#choix-coach').change(function () {
        if ($(this).is(":checked")) {
            $('.div-select-coach').show('500');
        }
        else {
            $('.div-select-coach').hide('500');
        }
    });
}

$(document).on('click','.bouton-annuler-reservation-employe',function() {
    let reservation = $(this).data('reservation');

    $.ajax({
        method  : 'POST',
        url     : 'annulerReservation',
        data    : {
            id_reservation : reservation
        },

        xhrFields: { withCredentials: true },
        crossDomain : true
    }).done(function(data) {
        alert(data);
        window.location.reload();
    })

});