@php
    $userModel = \App\Models\User::class;
    $userAuth = \Illuminate\Support\Facades\Auth::user();
@endphp

@extends('layout.layout')

@section('content')
    <div class="container">
        <h1 class="page-header">
            Mon compte
        </h1>

        @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @endif

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
                                <form class="form-horizontal" method="POST" action="{{ route('miseAJourCompte') }}">
                                    {{ csrf_field() }}

                                    <div class="form-group">
                                        <label for="first-name" class="col-md-4 control-label">Prénom</label>

                                        <div class="col-md-6">
                                            <input id="first-name" type="text" class="form-control" name="first-name" value="{{ $user->prenom_utilisateur }}" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="last-name" class="col-md-4 control-label">Nom</label>

                                        <div class="col-md-6">
                                            <input id="last-name" type="text" class="form-control" name="last-name" value="{{ $user->nom_utilisateur }}" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="birth-date" class="col-md-4 control-label">Date de naissance</label>

                                        <div class="col-md-6">
                                            <input id="birth-date" type="date" class="form-control" name="birth-date" value="{{ $user->date_naiss_utilisateur }}" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="email" class="col-md-4 control-label">E-mail</label>

                                        <div class="col-md-6">
                                            <input id="email" type="email" class="form-control" name="email" value="{{ $email }}" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                        <label for="password" class="col-md-4 control-label">Mot de passe</label>

                                        <div class="col-md-6">
                                            <input id="password" type="password" class="form-control" name="password" value="********" disabled>

                                            @if ($errors->has('password'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                                        <div class="col-md-6">
                                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" value="********" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="demande_relance" class="col-md-4 control-label">Demande de relance </label>

                                        <div class="col-md-6">
                                            <input id="demande_relance" type="checkbox" class="form-control" name="demande_relance"
                                                   @if($user->demande_relance)
                                                        checked
                                                   @endif
                                            >
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div id="divDelai">
                                            <label for="select_delai" class="col-md-4 control-label">Delai de relance (jours)</label>

                                            <div class="col-md-6">
                                                <select id="select_delai" class="form-control" name="select_delai">
                                                    @for ($i = 1; $i <= 7; $i++)
                                                        <option value="{{ $i }}"
                                                        @if($i == $user->delai_relance) selected @endif>{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-4">
                                            <button type="submit" class="btn btn-primary">
                                                Mettre à jour
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    @if($userModel::getUser($userAuth->id_utilisateur)->estClient())
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
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection