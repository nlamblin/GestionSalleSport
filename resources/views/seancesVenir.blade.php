@extends('layout.layout')

@section('content')
    <div class="container">
    	
    	<!--Dans le cas où la personne connectée est un client -->
    	@if($utilisateur->nom_statut == 'ROLE_CLIENT')
	        <h1 class="page-header">
	            {{ $utilisateur->prenom_utilisateur}}, voici vos séances à venir chez Fit'Miage
	        </h1>

	        <div class="row">
	        	<!--Pour chaque séance à venir -->
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
							<button type='button' data-reservation="{{ $reservation->id_reservation}}" class="btn btn-primary bouton-annuler-reservation"> Annuler réservation</button>
		            </div>

	            @endforeach
	        </div>
	    <!--Dans le cas où la personne connectée est un coach -->
        @elseif($utilisateur->nom_statut == 'ROLE_COACH')
        	<h1 class="page-header">
	            {{ $utilisateur->prenom_utilisateur}}, voici votre planning de coaching
	        </h1>

	        <div class="row">
	        	<!--Pour chaque séance à venir -->
	        	@foreach($seanceVenirCoach as $reservation)
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
		                	Nombre de participants : {{ $reservation->capacite_seance - $reservation->places_restantes}}
		                </h6>
		                <p>
		                	Date : {{ date('d/m/Y', strtotime($reservation->date_seance)) }} - Heure : {{ date('H:i', strtotime($reservation->heure_seance)) }}
		                </p>
		            </div>

	            @endforeach
	        </div>
        @endif



    </div>
@endsection