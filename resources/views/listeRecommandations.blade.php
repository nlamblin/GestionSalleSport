@extends('layout.layout')

@section('content')
    <div class="container">
        <h1 class="page-header">
           Recommandations
        </h1>

		@if(count($recommandationsAutresActivitesMemeDateHeure) > 0 || count($recommandationsMemeActiviteMemeHeure) > 0 || count($recommandationsMemeActiviteMemeDate) > 0)

			<div class="alert alert-info">
				Les réservations sont basées sur les même activtés, dates et horaires.
			</div>

			@if(count($recommandationsMemeActiviteMemeDate) > 0)

				<div class="row">
					<h4> Recommandations des séances sur la même activité pour le même jour </h4>
					<div class="recommandationsMemeActiviteMemeDate">
						<!--Pour chaque séance à venir -->
						@foreach($recommandationsMemeActiviteMemeDate as $recommandation)
							<div class="col-md-4 img-portfolio">
								<a href="#">
									<img class="img-responsive img-hover" src="http://placehold.it/700x400" alt="">
								</a>
								<h3>
									Activite : {{ $recommandation->nom_activite }}
								</h3>
								<h4>
									Séance : {{ $recommandation->type_seance }}
								</h4>
								<p>
									Date : {{ date('d/m/Y', strtotime($recommandation->date_seance)) }} - Heure : {{ date('H:i', strtotime($recommandation->heure_seance)) }}
								</p>
								<button data-placesRestantes="{{ $recommandation->places_restantes }}" data-seance="{{ $recommandation->id_seance }}" data-typeSeance="{{ $recommandation->type_seance }}" data-avecCoach="{{ $recommandation->avecCoach }}" class="btn btn-primary bouton-reservation" data-toggle="modal" data-target="#reservationModal">Réserver
								</button>
							</div>
						@endforeach
					</div>
				</div>

				<hr>

			@endif

			@if(count($recommandationsMemeActiviteMemeHeure) > 0)

			<div class="row">
				<h4> Recommandations des séances sur la même activité à la même heure </h4>
				<div class="recommandationsMemeActiviteMemeHeure">
					<!--Pour chaque séance à venir -->
					@foreach($recommandationsMemeActiviteMemeHeure as $recommandation)
						<div class="col-md-4 img-portfolio">
							<a href="#">
								<img class="img-responsive img-hover" src="http://placehold.it/700x400" alt="">
							</a>
							<h3>
								Activite : {{ $recommandation->nom_activite }}
							</h3>
							<h4>
								Séance : {{ $recommandation->type_seance }}
							</h4>
							<p>
								Date : {{ date('d/m/Y', strtotime($recommandation->date_seance)) }} - Heure : {{ date('H:i', strtotime($recommandation->heure_seance)) }}
							</p>
							<button data-placesRestantes="{{ $recommandation->places_restantes }}" data-seance="{{ $recommandation->id_seance }}" data-typeSeance="{{ $recommandation->type_seance }}" data-avecCoach="{{ $recommandation->avecCoach }}" class="btn btn-primary bouton-reservation" data-toggle="modal" data-target="#reservationModal">Réserver
							</button>
						</div>
					@endforeach
				</div>
			</div>

			<hr>

			@endif

			@if(count($recommandationsAutresActivitesMemeDateHeure) > 0)

				<div class="row">
					<h4> Recommandations des séances sur d'autres activités, même date même heure </h4>
					<div class="recommandationAutresActiviteMemeDateHeure">
						<!--Pour chaque séance à venir -->
						@foreach($recommandationsAutresActivitesMemeDateHeure as $recommandation)
							<div class="col-md-4 img-portfolio">
								<a href="#">
									<img class="img-responsive img-hover" src="http://placehold.it/700x400" alt="">
								</a>
								<h3>
									Activite : {{ $recommandation->nom_activite }}
								</h3>
								<h4>
									Séance : {{ $recommandation->type_seance }}
								</h4>
								<p>
									Date : {{ date('d/m/Y', strtotime($recommandation->date_seance)) }} - Heure : {{ date('H:i', strtotime($recommandation->heure_seance)) }}
								</p>
								<button data-placesRestantes="{{ $recommandation->places_restantes }}" data-seance="{{ $recommandation->id_seance }}" data-typeSeance="{{ $recommandation->type_seance }}" data-avecCoach="{{ $recommandation->avecCoach }}" class="btn btn-primary bouton-reservation" data-toggle="modal" data-target="#reservationModal">Réserver
								</button>
							</div>
						@endforeach
					</div>
				</div>

			@endif
		@else
			<div class="alert alert-warning">
				Nous ne possédons pas de recommandations correspondant à votre séance selon nos critères.
			</div>
		@endif

    </div>
@endsection

@include('modalReservation')