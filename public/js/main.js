$(document).ready(function () {

    // configuration de ajax pour pouvoir utiliser POST
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // affichage / ou non de délai de relance
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
        let type = document.getElementsByTagName("type_seance")[0];
        if(type ==='collective'){
            $('#divPlacesSeances').show('500');
        }
        else{
            $('#divPlacesSeances').hide('500');
        }
    });

    //Permet de verifier la date du formulaire de création de séance
    // ( "#date_seance" ).datepicker({ minDate: 'today'});
    
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

    // appel ajax pour annuler la reservation
    $('.bouton-annuler-reservation').on('click', function() {
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
        })
    });

});



//**********************************************************************
//*********************** MODAL POUR LA RESERVATION ********************
//**********************************************************************

// fonction appelée à l'ouverture du modal pour la réservation
$(document).on('show.bs.modal', '#reservationModal', function (e) {

    let typeSeance = $(e.relatedTarget).data('typeseance');

    if(typeSeance === 'collective') {
        $('.div-choix-coach').hide();
        $('.div-select-coach').hide();
    }
    else if(typeSeance === 'individuelle') {
        $.ajax({
            method : 'GET',
            url : 'coachsDisponibles',
            data : {
                'idSeance' : $(e.relatedTarget).data('seance')
            },
            xhrFields: { withCredentials: true },
            crossDomain : true
        }).done(function(data) {
            $.each(data, function (i, item) {
                let option = new Option(item.prenom_utilisateur + ' ' + item.nom_utilisateur, item.id_utilisateur);
                $('#select-coach').append(option);
            });
        });
    }

    let personnesAAjouter = [];

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
            console.log(personnesAAjouter);
        }

        // quand on supprime une personne
        $('.removePersonne').on('click', function () {
            idUtilisateur = $(this).data('idutilisateur');
            delete personnesAAjouter[idUtilisateur];
            $('li').remove('#li-utilisateur-' + idUtilisateur);
        });
    });

    // affichage / ou non du choix du coach
    $('#choix-coach').change(function () {
        if ($(this).is(":checked")) {
            $('.div-select-coach').show('500');
        }
        else {
            $('.div-select-coach').hide('500');
        }
    });

    // ajax pour effectuer la réservation
    $('#reservation-seance').on('click', function() {

        let idCoach = null;

        if ($('#choix-coach').is(':checked')) {
            idCoach = $('#select-coach').val();
        }

        let idsPersonnes = idPersonnesAAjouter(personnesAAjouter);

        $.ajax({
            method  : 'PUT',
            url     : 'effectuerReservation',
            data    : {
                'idSeance'          : $(e.relatedTarget).data('seance'),
                'personnesAAjouter' : idsPersonnes,
                'idCoach'           : idCoach
            },
            xhrFields: { withCredentials: true },
            crossDomain : true
        }).done(function (data) {
            // fermeture du modal
            $('.reservationModal').modal('hide');
            // affichage du message concernant la reservation
            alert(data);
            // rechargement de la page
            window.location.reload();
        });
    });

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

