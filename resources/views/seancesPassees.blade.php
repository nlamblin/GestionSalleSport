@extends('layout.layout')

@section('content')
    <div class="container">
        <h1 class="page-header">
            Mes séances passées
        </h1>

        @if(count($reservationPassees) > 0)
	        <div class="row">
	        	<!--Pour chaque séance à venir -->
	        	@foreach($reservationPassees as $reservation)
		            <div class="col-md-4 img-portfolio">
		                <a href="portfolio-item.html">
		                    <img class="img-responsive img-hover" src="http://placehold.it/700x400" alt="">
		                </a>
		                <h3>
		                    Activite : {{ $reservation->nom_activite }}
		                </h3>
		                <h4> 
		                	Séance : {{ $reservation->type_seance }}
		                </h4>
		              	<p>
		                	Date : {{ date('d/m/Y', strtotime($reservation->date_seance)) }} - Heure : {{ date('H:i', strtotime($reservation->heure_seance)) }}
		                </p>
		               
		            </div>

	            @endforeach
	        </div>
	        <!-- /.row -->
	    @else
	    	<div class="alert alert-warning">
				Vous n'avez effectué encore aucune séance.
			</div>
	    @endif

    </div>
@endsection