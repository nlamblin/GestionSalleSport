@extends('layout.layout')

@section('content')
    <div class="container">
    	
    	<!--Dans le cas où la personne connectée est un client -->
    	@if($utilisateur->nom_statut == 'ROLE_CLIENT')
	        <h1 class="page-header">
	            {{ $utilisateur->prenom_utilisateur}}, voici vos séances à venir chez Fit'Miage
	        </h1>

	        @if(count($reservationVenirClient) > 0)
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
		     @else
		     	<div class="alert alert-warning">
					Vous n'avez aucune séance à venir.
				</div>
		     @endif

	    <!--Dans le cas où la personne connectée est un coach -->
        @elseif($utilisateur->nom_statut == 'ROLE_COACH')
        	<h1 class="page-header">
	            {{ $utilisateur->prenom_utilisateur}}, voici votre planning de coaching
	        </h1>
	        @if(count($seanceVenirCoach) > 0)
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
			                	Le : {{ date('d/m/Y', strtotime($reservation->date_seance)) }} à {{ date('H:i', strtotime($reservation->heure_seance)) }}
			                </h5>
			                <p>
			                	Niveau : {{ $reservation->niveau_seance}}
			                </p>
			                <p>
			                	Capacité : {{ $reservation->capacite_seance }}
			                </p>
			                <p>
			                	Nombre de participants : 
			                	@if ( $reservation->capacite_seance - $reservation->places_restantes == 0 )
									Aucune inscription
			                	@else
			                	{{ $reservation->capacite_seance - $reservation->places_restantes}}
			                	@endif
			                </p>
			            </div>

		            @endforeach
		        </div>
	        @else
		       <div class="alert alert-warning">
					Vous n'avez aucun coaching à venir.
				</div>
		    @endif
        @endif
    </div>
@endsection