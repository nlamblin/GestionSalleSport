<div class="listeSeancesVenirClient">
	<!--Pour chaque séance à venir -->
    @if(count($reservationVenirClient) > 0 )
        	@foreach($reservationVenirClient as $reservation)
                <div class="col-md-4 img-portfolio">
                    <a href="#">
                        <img class="img-responsive img-hover" src="http://placehold.it/700x400" alt="">
                    </a>
                    <h3>
                        Activite : {{ $reservation->nom_activite }}
                    </h3>
                    <h4> 
                    	Séance : {{ $reservation->type_seance }}
                    </h4>
                    <h5>
                    	Niveau : {{ $reservation->niveau_seance}}
                    </h5>
                    <h6>
                    	Capacite : {{ $reservation->capacite_seance}}
                    </h6>
                    <p>
                    	Date : {{ date('d/m/Y', strtotime($reservation->date_seance)) }} - Heure : {{ date('H:i', strtotime($reservation->heure_seance)) }}
                    </p>
        			<button type='button'
                        data-reservation="{{ $reservation->id_reservation}}"
                        class="btn btn-primary bouton-annuler-reservation">
                        Annuler réservation
                    </button>
                </div>

            @endforeach
    @else
        <div class="alert alert-warning">
                Ce client n'a aucune réservation enregistrée.
        </div>
    @endif
</div>