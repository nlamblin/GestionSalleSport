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
        console.log($(this).data('seance'));
        $.ajax({
            method : 'POST',
            url : 'ajax/validUser',
            xhrFields: { withCredentials: true },
            crossDomain : true
        }).done(function (data) {

        });
    });

});