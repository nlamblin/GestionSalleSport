@extends('layout.layout')

@section('content')
    <div class="container">
        <h1 class="page-header">
            Création d'une activité
        </h1>

        <div class="row">
            <div class="col-lg-12">

                @if(session()->has('message'))
                    <div class="alert alert-success">
                        {{ session()->get('message') }}
                    </div>
                @endif

                <form class="form-horizontal" method="POST" action="{{ route('admin/creerActivite') }}">
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
    </div>
@endsection