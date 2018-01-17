@extends('layout.layout')

@section('content')

<div class="container">
    <h1 class="page-header">
     	Toutes les séances proposées par notre salle partenaire
    </h1>

    @if($utilisateurValide == false)
        <div class="alert alert-danger">
            Votre abonnement ou votre carte n'est pas valide. Vous ne pourrez pas réserver.
            <a href="{{ url('/compte') }}" class="button">Prendre un abonnement</a>
        </div>
    @endif

    @foreach($listeSeances as $seance)
        <div class="row">
            <div class="col-md-7">
                <a href="#">
                    <img class="img-responsive img-hover" src="http://placehold.it/700x300" alt="">
                </a>
            </div>
            <div class="col-md-5">
                <h4>Séance : {{ $seance->typeActivite }}</h4>
                <div class = "row">
                        <span>
                        	Coach
                            @if(is_null($seance->idUtilisateur))
                                non présent
                            @else
                                présent
                            @endif
                        </span>
                </div>
                <div class="row">
                    <span> Date : {{ date('d/m/Y', strtotime($seance->dateSeance)) }} </span>
                </div>
                <div class="row">
                    <span> Capacité : {{ $seance->capacite }} </span>
                </div>
                <div class="row">
                    <button class="btn btn-primary bouton-reservation-salle-externe" data-idseance="{{ $seance->idSeance  }}"
                        @if($utilisateurValide == false) disabled @endif
                    >
                        Réserver dans la salle partenaire
                    </button>
                </div>
            </div>
        </div>
        <!-- /.row -->

        <hr>

@endforeach

@endsection