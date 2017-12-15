@extends('layout.layout')

@section('css', asset('css/home.css'))


@section('content')
    <div class="container">
        <h1 class="page-header">
            Séances
        </h1>

        <div class="row">
            <!-- Menu de selection de l'activite -->

            <label for="select_activite" class="col-md-4 control-label">Choisissez une activité : </label>
            <div class="col-md-6">
                <select id="select_activite" class="form-control" name="select_activite">
                    @foreach($listeActivites as $activite)
                        <option value=$value.id_activite>{{$activite->nom_activite}}</option>
                    @endforeach
                </select>
            </div>

        </div>
       <!-- Project One -->
       @foreach($listeSeances as $seance)
        <div class="row">
            <div class="col-md-7">
                <a href="#">
                    <img class="img-responsive img-hover" src="http://placehold.it/700x300" alt="">
                </a>
            </div>
            <div class="col-md-5">
                <h3>Activité : {{$seance->nom_activite}} </h3>
                <h4>Séance : {{$seance->type_seance}}</h4>
                <div class = "row">
                    <span> 
                        Niveau : {{$seance->niveau_seance}}
                    </span>
                </div>
                <div class = "row">
                    <span> 
                        @if($seance->type_seance=='individuelle')
                        Coach personnel : 
                            @if($seance->avec_coach==true)
                                disponible
                            @else
                                indisponible
                            @endif
                        @else
                        Coach collectif :
                        @if($seance->avec_coach==true)
                                disponible
                            @else
                                indisponible
                            @endif
                        @endif
                    </span>
                </div>
                <div class = "row">
                    <span> 
                        Date : {{$seance->date_seance}} - Heure : {{$seance->heure_seance}}
                    </span>
                </div>
                <div class = "row">
                    <span> 
                        Capacité : {{$seance->capacite_seance}}
                    </span>
                </div>
                <div class = "row">
                    <span> 
                        Places restantes : {{$seance->places_restantes}}
                    </span>
                </div>
            </div>
        </div>
        <!-- /.row -->

        <hr>
        @endforeach     
        
        <!-- Pagination -->
        <div class="row text-center">
            <div class="col-lg-12">
                <ul class="pagination">
                    <li>
                        <a href="#">&laquo;</a>
                    </li>
                    <li class="active">
                        <a href="#">1</a>
                    </li>
                    <li>
                        <a href="#">2</a>
                    </li>
                    <li>
                        <a href="#">3</a>
                    </li>
                    <li>
                        <a href="#">4</a>
                    </li>
                    <li>
                        <a href="#">5</a>
                    </li>
                    <li>
                        <a href="#">&raquo;</a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- /.row -->
        <hr>
    </div>

@endsection