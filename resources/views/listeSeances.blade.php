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
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#reservationModal">Réserver</a>
                </div>
            </div>
        </div>
        <!-- /.row -->

        <hr>
    @endforeach
</div>

<div class="modal fade" id="reservationModal" tabindex="-1" role="dialog" aria-labelledby="reservationModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Basic Modal</h4>
            </div>
            <div class="modal-body">
                <h3>Modal Body</h3>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>