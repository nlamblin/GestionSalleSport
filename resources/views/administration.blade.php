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
                            <div class="panel-heading">Création d'une nouvelle activité</div>
                                <div class="panel-body">
                                    <form id="form-activite" class="form-horizontal" method="POST" 
                                    action="{{action('AdministrationController@creerActivite')}}">
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
                                                <button type="submit" class="btn btn-primary" form="form-activite">
                                                    Enregistrer l'activité
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">Création d'une séance</a>
                            </h4>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse">
                            <div class="panel-body">
                                Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection