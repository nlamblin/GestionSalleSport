@extends('layout.layout')

@section('content')
    <div class="container">
        <h1 class="page-header">
            Administration
        </h1>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Création d'une activité</a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse">
                            <form class="form-horizontal" method="POST" action="{{ route('creerActivite') }}">
                                {{ csrf_field()}}
                                <div class="form-group">
                                    <label for="nom_activite" class="col-md-4 control-label">Nom de la nouvelle activite</label>

                                    <div class="col-md-6">
                                        <input id="nom_activite" class="form-control" name="nom_activite" required autofocus>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary">
                                            Enregistrer l'activité
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">Création d'une nouvelle séance</a>
                            </h4>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse">
                            <form class="form-horizontal" method="POST" action="{{ route('creerSeance') }}">
                                {{ csrf_field()}}
                                <div class="form-group">
                                    <label for="choix_activite" class="col-md-4 control-label">Activité</label>
                                    
                                    <div class="col-md-6">
                                        <ul class="input">
                                            <select id="activite_seance" class="form-control" name="activite_seance">
                                                @foreach($listeActivites as $activite)
                                                    <option value="{{ $activite->id_activite }}">
                                                        {{ $activite->nom_activite }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </ul>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="type_seance" class="col-md-4 control-label">Type</label>
                                    
                                    <div class="col-md-6">
                                        <ul class="input">
                                            <select id="type_seance" class="form-control" name="type_seance">
                                                <option value="collective">Collective</option>
                                                <option value="individuelle">Individuelle</option>
                                            </select>
                                        </ul>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="niveau_seance" class="col-md-4 control-label">Niveau</label>
                                    
                                    <div class="col-md-6">
                                        <ul class="input">
                                            <select id="niveau_seance" class="form-control" name="niveau_seance">
                                                <option value="debutant">Debutant</option>
                                                <option value="intermediaire">Intermediaire</option>
                                                <option value="expert">Expert</option>
                                            </select>
                                        </ul>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="date_seance" class="col-md-4 control-label">Date : </label>
                                    <div class="col-md-6">
                                        <input type="date" max="2021-12-31" min="time()" class="form-control" name="date_seance" required >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="heure_seance" class="col-md-4 control-label">Heure : </label>
                                    <div class="col-md-6">
                                        <input type="time" step='60' class="form-control" name="heure_seance" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="coach_seance" class="col-md-4 control-label">Coach disponible </label>

                                    <div class="col-md-6">
                                        <input id="coach_seance" type="checkbox" class="form-control" name="coach_seance" checked>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div id="divPlacesSeances">
                                        <label for="places_seance" class="col-md-4 control-label">Nombre de places</label>

                                        <div class="col-md-6">
                                           <input type="number" min='1' class="form-control" name="places_seance" value='1'required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary">
                                            Enregistrer la séance
                                        </button>
                                    </div>
                                </div>


                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection