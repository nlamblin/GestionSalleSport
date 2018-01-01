@extends('layout.layout')

@section('content')
    <div class="container">
        <h1 class="page-header">
            Séances
        </h1>

        <div class="row rowSelectActivite">
            <!-- Menu de selection de l'activite -->

            <label for="select_activite" class="col-md-4 control-label">Choisissez une activité : </label>
            <div class="col-md-6">
                <select id="select_activite" class="form-control" name="select_activite">
                    <option value="default">Selectionner une activité</option>
                    @foreach($listeActivites as $activite)
                        <option value={{ $activite->id_activite }}>{{ $activite->nom_activite }}</option>
                    @endforeach
                </select>
            </div>

        </div>
        <hr>
    </div>

@endsection