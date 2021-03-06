@extends('layout.layout')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Inscription</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('register') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('first-name') ? ' has-error' : '' }}">
                            <label for="first-name" class="col-md-4 control-label">Prénom</label>

                            <div class="col-md-6">
                                <input id="first-name" type="text" class="form-control" name="first-name" value="{{ old('first-name') }}" required autofocus>

                                @if ($errors->has('first-name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('first-name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('last-name') ? ' has-error' : '' }}">
                            <label for="last-name" class="col-md-4 control-label">Nom</label>

                            <div class="col-md-6">
                                <input id="last-name" type="text" class="form-control" name="last-name" value="{{ old('last-name') }}" required autofocus>

                                @if ($errors->has('last-name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('last-name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('birth-date') ? ' has-error' : '' }}">
                            <label for="birth-date" class="col-md-4 control-label">Date de naissance</label>

                            <div class="col-md-6">
                                <input id="birth-date" type="date" class="form-control" name="birth-date" value="{{ old('birth-date') }}" required>

                                @if ($errors->has('birth-date'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('birth-date') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-mail</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Mot de passe</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

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
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="demande_relance" class="col-md-4 control-label">Demande de relance </label>

                            <div class="col-md-6">
                                <input id="demande_relance" type="checkbox" class="form-control" name="demande_relance" checked>
                            </div>
                        </div>

                        <div class="form-group">
                            <div id="divDelai">
                                <label for="select_delai" class="col-md-4 control-label">Delai de relance (jours)</label>

                                <div class="col-md-6">
                                        <select id="select_delai" class="form-control" name="select_delai">
                                            <option value="1" selected>1</option>
                                            <option value="2" >2</option>
                                            <option value="3" >3</option>
                                            <option value="4" >4</option>
                                            <option value="5" >5</option>
                                            <option value="6" >6</option>
                                            <option value="7" >7</option>
                                        </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    S'inscrire
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
