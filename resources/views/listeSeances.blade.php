<div class="listeSeances">

    @if($utilisateurValide == false)
        <div class="alert alert-danger">
            Votre abonnement ou votre carte n'est pas valide, vous ne pourrez pas effectuer de réservation.
        </div>
    @endif

    <!-- Project One -->
    @foreach($listeSeances as $seance)
        <div class="row">
            <div class="col-md-7">
                <a href="#">
                    <img class="img-responsive img-hover" src="http://placehold.it/700x300" alt="">
                </a>
            </div>
            <div class="col-md-5">
                <h3>Activité : {{ $seance->nom_activite }} </h3>
                <h4>Séance : {{ $seance->type_seance }}</h4>
                <div class = "row">
                        <span>
                            Niveau : {{ $seance->niveau_seance }}
                        </span>
                </div>
                <div class = "row">
                        <span>
                        	Coach 
                                @if($seance->avec_coach == true)
                                	@if(($seance->type_seance) === "collective")
                                		collectif : présent
                                	@else
                                		personnel : disponible
                                	@endif
                                @else
                                    @if(($seance->type_seance) === "collective")
                                		collectif : non présent
                                	@else
                                		personnel : non disponible
                                	@endif
                                @endif
                        </span>
                </div>
                <div class="row">
                    <span> Date : {{ date('d/m/Y', strtotime($seance->date_seance)) }} - Heure : {{ date('H:i', strtotime($seance->heure_seance)) }} </span>
                </div>
                <div class="row">
                    <span> Capacité : {{ $seance->capacite_seance }} </span>
                </div>
                <div class="row">
                    <span> Places restantes : {{ $seance->places_restantes }}</span>
                </div>
                <div class="row">
                    <button data-seance="{{ $seance->id_seance }}" id="buttonReservationModal" class="btn btn-primary bouton-reservation" data-toggle="modal" data-target="#reservationModal"
                            @if($utilisateurValide == false)
                            disabled
                            @endif>Réserver
                    </button>

                </div>
            </div>
        </div>
        <!-- /.row -->

        <hr>

    @endforeach

</div>

<div class="modal fade" id="reservationModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Réservation</h4>
            </div>

            <div class="modal-body">
                <form class="form-horizontal reservationForm">

                    <div class="form-group">
                        <label for="ajout-personne" class="col-md-4 control-label">Ajouter des personnes</label>

                        <div class="col-md-6">
                            <ul class="input-group">
                                <select id="ajout-personne" class="form-control" name="ajout-personne">
                                    <option value="default">Selectionner les personnes à ajouter</option>
                                    <option value="1" data-name="Lamblin" data-prenom="Nicolas" data-email="nico@gmail.com">Nico</option>
                                    <option value="2" data-name="Thiriot" data-prenom="Anais" data-email="anais@gmail.com">Anais</option>
                                    <option value="3" data-name="Mansuy" data-prenom="Claire" data-email="claire@gmail.com">Claire</option>
                                    <option value="4" data-name="Fernandes" data-prenom="Charlotte" data-email="charlotte@gmail.com">Charlotte</option>
                                </select>

                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-primary bouton-ajout-personne">Ajouter</button>
                                </span>
                            </ul>

                            <ul class="list-group listePersonneAAjouter">

                            </ul>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="choix-coach" class="col-md-4 control-label"> Souhaitez-vous un coach lors de la séance ?</label>

                        <div class="col-md-6">
                            <input id="choix-coach" type="checkbox" class="form-control" name="choix-coach" checked>
                        </div>
                    </div>

                    <div class="form-group div-choix-coach">
                        <label for="select-coach" class="col-md-4 control-label"> Veuillez choisir votre coach</label>

                        <div class="col-md-6">
                            <select id="select-coach-" class="form-control" name="select-coach">
                                <option value="default">Selectionner un coach</option>
                                <option value="1">Coach 1</option>
                                <option value="2">Coach 2</option>
                                <option value="3">Coach 3</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="reservation-seance">Réserver</button>
            </div>
        </div>
    </div>
</div>
