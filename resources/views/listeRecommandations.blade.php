@extends('layout.layout')

@section('css', asset('css/home.css'))

@section('content')
    <div class="container">
        <h1 class="page-header">
           Recommandations trouvées
        </h1>


        <h2> Recommandations des séances sur la même activité pour le même jour </h2>
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
		            	Id : {{ $recommandation->id_seance }}
		            </h4>
		            <p>
		            	Date : {{ date('d/m/Y', strtotime($recommandation->date_seance)) }} - Heure : {{ date('H:i', strtotime($recommandation->heure_seance)) }}
		            </p>
		        </div>

		    @endforeach
		</div>

		<h2> Recommandations des séances sur la même activité à la même heure </h2>
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
		            	Id : {{ $recommandation->id_seance }}
		            </h4>
		            <p>
		            	Date : {{ date('d/m/Y', strtotime($recommandation->date_seance)) }} - Heure : {{ date('H:i', strtotime($recommandation->heure_seance)) }}
		            </p>
		        </div>

		    @endforeach
		</div>

		<h2> Recommandations des séances sur d'autres activités, même date même heure </h2>
		<div class="recommandationAutresActiviteMemeDateHeure">
			<!--Pour chaque séance à venir -->
			@foreach($recommandationAutresActiviteMemeDateHeure as $recommandation)
		        <div class="col-md-4 img-portfolio">
		            <a href="#">
		                <img class="img-responsive img-hover" src="http://placehold.it/700x400" alt="">
		            </a>
		            <h3>
		                Activite : {{ $recommandation->nom_activite }}
		            </h3>
		            <h4> 
		            	Séance : {{ $recommandation->type_seance }}
		            	Id : {{ $recommandation->id_seance }}
		            </h4>
		            <p>
		            	Date : {{ date('d/m/Y', strtotime($recommandation->date_seance)) }} - Heure : {{ date('H:i', strtotime($recommandation->heure_seance)) }}
		            </p>
		        </div>

		    @endforeach
		</div>

    </div>

@endsection