<div class="listeSeances">
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
                            @if($seance->type_seance=='individuelle')
                                Coach personnel :
                                @if($seance->avec_coach==true)
                                    disponible
                                @else
                                    indisponible
                                @endif
                            @else
                                Coach collectif :
                                @if($seance->avec_coach==true)
                                    disponible
                                @else
                                    indisponible
                                @endif
                            @endif
                        </span>
                </div>
                <div class="row">
                    <span> Date : {{ $seance->date_seance }} - Heure : {{ $seance->heure_seance }} </span>
                </div>
                <div class="row">
                    <span> Capacité : {{ $seance->capacite_seance }} </span>
                </div>
                <div class="row">
                    <span> Places restantes : {{ $seance->places_restantes }}</span>
                </div>
                <div class="row">
                    <button class="btn btn-primary bouton-reservation" data-toggle="modal" data-target="#reservationModal-{{ $seance->id_seance }}">Réserver</button>
                </div>
            </div>
        </div>
        <!-- /.row -->

        <hr>

        <div class="modal fade reservationModal" id="reservationModal-{{ $seance->id_seance }}" data-seance='{{ $seance->id_seance }}' tabindex="-1" role="dialog" aria-labelledby="reservationModal-{{ $seance->id_seance }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Reservation</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal">

                            <div class="form-group">
                                <label for="ajout-personne" class="col-md-4 control-label">Ajouter des personnes</label>

                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input id="ajout-personne" type="text" class="form-control" name="ajout-personne" placeholder="email@gmail.com">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-primary bouton-ajout-personne">Ajouter</button>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-primary">Réserver</button>
                    </div>
                </div>
            </div>
        </div>

    @endforeach
</div>