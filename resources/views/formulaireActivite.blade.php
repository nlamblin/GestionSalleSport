@extends('layout.layout')

@section('css', asset('css/home.css'))


@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Création d'une nouvelle activité</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('home') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('nom-activite') ? ' has-error' : '' }}">
                            <label for="nom-activte" class="col-md-4 control-label">Saissisez le nom de l'activité : </label>

                            <div class="col-md-6">
                                <input id="nom-activite" type="text" class="form-control" name="nom-activite" value="{{ old('nom-activite') }}" required autofocus>

                                @if ($errors->has('nom-activite'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nom-activite') }}</strong>
                                    </span>
                                @endif
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
    </div>
</div>
@endsection