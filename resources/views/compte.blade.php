@extends('layout.layout')

@section('content')
    <div class="container">
        <h1 class="page-header">
            Mon compte
        </h1>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseInformations">Mes informations</a>
                            </h4>
                        </div>
                        <div id="collapseInformations" class="panel-collapse collapse">
                            <div class="panel-body">
                                Cette fonctionnalité n'est pas encore disponible.
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseAboCarte">Abonnement / Carte</a>
                            </h4>
                        </div>
                        <div id="collapseAboCarte" class="panel-collapse collapse">
                            <div class="panel-body">
                                @if(isset($carte))
                                    <div class="alert alert-info">
                                        Vous possédez déjà une carte possedant {{ $carte->seance_dispo }} séance(s) disponible(s).
                                    </div>
                                @elseif(isset($abonnement))
                                    <div class="alert alert-info">
                                        Vous possedez déjà un abonnement {{ $abonnement->type_abo }} valable jusqu'au {{ date('d/m/Y', strtotime($abonnement->date_fin_abo)) }}.
                                    </div>
                                @else
                                    <form class="form-horizontal prendreAboCarteForm">

                                        <div class="form-group">
                                            <label for="choix-type-abo" class="col-md-4 control-label">Type de l'abonnement</label>

                                            <div class="col-md-6">

                                                <select id="choix-type-abo"  class="form-control" name="choix-type-abo">
                                                    <option value="default">Selectionner votre type d'abonnement</option>
                                                    <option value="carte">Carte de 10 séances</option>
                                                    <option value="abo-annuel">Abonnement mensuel</option>
                                                    <option value="abo-mensuel">Abonnement annuel</option>
                                                </select>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-md-6 col-md-offset-4">
                                                <button type="button" class="btn btn-primary" id="achat-abonnement">Acheter</button>
                                            </div>
                                        </div>

                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection