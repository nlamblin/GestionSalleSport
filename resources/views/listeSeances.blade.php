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
                                	@if($seance->type_seance == "collective")
                                		collectif : présent
                                	@else
                                		personnel : disponible
                                	@endif
                                @else
                                    @if($seance->type_seance == "collective")
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
                    <button data-hrefRecommandations="{{ url('client/recommandationsSeances/' . $seance->id_seance) }}"
                            data-placesRestantes="{{ $seance->places_restantes }}"
                            data-seance="{{ $seance->id_seance }}"
                            data-typeSeance="{{ $seance->type_seance }}"
                            data-avecCoach="{{ $seance->avec_coach }}"
                            class="btn btn-primary bouton-reservation" data-toggle="modal" data-target="#reservationModal">
                        Réserver
                    </button>
                </div>
            </div>
        </div>
        <!-- /.row -->

        <hr>

    @endforeach

</div>

@include('modalReservation')