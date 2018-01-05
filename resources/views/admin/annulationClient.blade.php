@extends('layout.layout')

@section('content')
    <div class="container">
        <h1 class="page-header">
            Annuler une reservation client
        </h1>

        <div class="row rowSelectClientAnnulation">
            <!-- Menu de selection du client -->

            <label for="select_client_annulation" class="col-md-4 control-label">Choisissez un client : </label>
            <div class="col-md-6">
                <select id="select_client_annulation" class="form-control" name="select_client_annulation">
                    <option value="default">Selectionner un client</option>
                    @foreach($utilisateur as $client)
                        <option value={{ $client->id_utilisateur }}>{{ $client->email }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <hr>
    </div>
@endsection