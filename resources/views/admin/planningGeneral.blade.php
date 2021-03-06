@extends('layout.layout')

@section('content')

<div class="container">

    <h1 class="page-header">
        Voici le planning général des séances chez Fit'Miage
    </h1>
    @if(count($listeSeances) > 0)
        <div class="row">
            <!--Pour chaque séance à venir -->
            @foreach($listeSeances as $reservation)
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
                    
                </div>

            @endforeach
        </div>
    @else
        <div class="alert alert-warning">
            Il n'y a aucune séance à venir.
        </div>
    @endif

@endsection