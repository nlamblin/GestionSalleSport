$(document).ready(function () {

    // affichage / ou non de délai de relance
    $('#demande_relance').change(function()  {
        if($(this).is(":checked")) {
           $('#divDelai').show('500');
        }
        else {
            $('#divDelai').hide('500');
        }
    });

    // appel ajax pour récupérer les seances disponibles en fonction des activités
    $('#select_activite').change(function() {
        // on supprime les seances deja affichées
        $('.listeSeances').remove();

        // si ce n'est pas l'option par defaut
        if($(this).val() !== 'default') {

            $.ajax({
                method : 'GET',
                url : 'ajax/listeSeances/',
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

    // fonction appelée à l'ouverture du modal pour les reservations
    $(document).on('show.bs.modal', '.reservationModal', function (e) {

        var id_seance = $(this).data('seance')
            , personneAAjouter = {};

        // quand on ajoute une personne
        $('.bouton-ajout-personne').on('click', function () {
            var idUtilisateur = $('#ajout-personne-' + id_seance).val()

            // Si la personne n'a pas déjà été ajoutée
            if (personneAAjouter[idUtilisateur] === undefined) {

                var optionSelected = $('#ajout-personne-' + id_seance + ' option:selected')[0]
                    , nomUtilisateur = $(optionSelected).data('name')
                    , prenomUtilisateur = $(optionSelected).data('prenom')
                    , emailUtilisateur = $(optionSelected).data('email');

                personneAAjouter[idUtilisateur] = [];
                personneAAjouter[idUtilisateur]['nom'] = nomUtilisateur;
                personneAAjouter[idUtilisateur]['prenom'] = prenomUtilisateur;
                personneAAjouter[idUtilisateur]['email'] = emailUtilisateur;

                // ajout de la personne dans la liste
                $('.listePersonneAAjouter').append('<li class="list-group-item" id="li-utilisateur-' + idUtilisateur + '"><span data-idUtilisateur="' + idUtilisateur + '" class="badge removePersonne">X</span>' + prenomUtilisateur + ' ' + nomUtilisateur + ' &lt' + emailUtilisateur + '&gt </li>');
            }

            // quand on supprime une personne
            $('.removePersonne').on('click', function () {
                idUtilisateur = $(this).data('idutilisateur');
                delete personneAAjouter[idUtilisateur];
                $('li').remove('#li-utilisateur-' + idUtilisateur);
            });
        });

        // affichage / ou non du choix du coach
        $('#choix-coach').change(function () {
            if ($(this).is(":checked")) {
                $('.div-choix-coach').show('500');
            }
            else {
                $('.div-choix-coach').hide('500');
            }
        });
    });

    // fonction appelée à la fermeture du modal
    $(document).on('hide.bs.modal', '.reservationModal', function(e) {
        $('.listePersonneAAjouter').children().remove();
    });

});