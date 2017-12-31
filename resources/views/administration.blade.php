@extends('layout.layout')

@section('content')
    <div class="container">
        <h1 class="page-header">
            Administration
        </h1>

        <div class="row">
            <div class="col-lg-12">

                @if(session()->has('message'))
                    <div class="alert alert-success">
                        {{ session()->get('message') }}
                    </div>
                @endif

                <div class="panel-group" id="accordion">

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseAjoutEmploye">Ajout d'un employé</a>
                            </h4>
                        </div>
                        <div id="collapseAjoutEmploye" class="panel-collapse collapse">
                            <form class="form-horizontal" method="POST" action="{{ route('ajouterEmploye') }}">
                                {{ csrf_field()}}

                                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                                    <label for="ajout-employe-email" class="col-md-4 control-label">Email </label>

                                    <div class="col-md-6">
                                        <input id="ajout-employe-email" type="text" class="form-control" name="email">

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

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseAjoutCoach">Ajout d'un coach</a>
                            </h4>
                        </div>
                        <div id="collapseAjoutCoach" class="panel-collapse collapse">
                            <form class="form-horizontal" method="POST" action="{{ route('ajouterCoach') }}">
                                {{ csrf_field()}}

                                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                                    <label for="ajout-coach-email" class="col-md-4 control-label">Email </label>

                                    <div class="col-md-6">
                                        <input id="ajout-coach-email" type="text" class="form-control" name="email">

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
            </div>
        </div>
    </div>
@endsection