@extends('layout.layout')

@section('content')
    <div class="container">
        <h1 class="page-header">
            Ajout d'une personne en tant que coach
        </h1>

        <div class="row">
            <div class="col-lg-12">

                @if(session()->has('message'))
                    <div class="alert alert-success">
                        {{ session()->get('message') }}
                    </div>
                @endif

                @if(session()->has('messageWarning'))
                    <div class="alert alert-warning">
                        {{ session()->get('messageWarning') }}
                    </div>
                @endif

                @if(session()->has('messageDanger'))
                    <div class="alert alert-danger">
                        {{ session()->get('messageDanger') }}
                    </div>
                @endif

                <form class="form-horizontal" method="POST" action="{{ route('admin/ajouterCoach') }}">
                    {{ csrf_field()}}

                    <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                        <label for="ajout-coach-email" class="col-md-4 control-label">Email </label>

                        <div class="col-md-6">
                            <input id="ajout-coach-email" type="text" class="form-control" name="email" value="{{ old('email') }}">

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
                                Enregistrer le coach
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection