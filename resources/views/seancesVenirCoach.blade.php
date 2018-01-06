@extends('layout.layout')

@section('content')
    <div class="container">

        <h1 class="page-header">
            {{ $utilisateur->prenom_utilisateur}}, voici votre planning de coaching
        </h1>
        @if(count($seanceVenirCoach) > 0)
            <div class="row">
                <!--Pour chaque séance à venir -->
                @foreach($seancesVenir as $reservation)
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
                                {{ $reservation->capacite_seance - $reservation->places_restantes }}
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
    </div>
@endsection