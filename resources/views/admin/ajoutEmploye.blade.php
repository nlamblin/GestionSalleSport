@extends('layout.layout')

@section('content')
    <div class="container">
        <h1 class="page-header">
            Ajout d'un employé
        </h1>

        <div class="row">
            <div class="col-lg-12">

                @if(session()->has('message'))
                    <div class="alert alert-success">
                        {{ session()->get('message') }}
                    </div>
                @endif

                <form class="form-horizontal" method="POST" action="{{ route('admin/ajouterEmploye') }}">
                    {{ csrf_field()}}

                    <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                        <label for="ajout-employe-email" class="col-md-4 control-label">Email </label>

                        <div class="col-md-6">
                            <input id="ajout-employe-email" type="text" class="form-control" name="email" value="{{ old('email') }}">

                            @if ($errors->has('email'))
                                <span class="help-block">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                Enregistrer l'employé
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection