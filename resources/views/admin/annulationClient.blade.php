@extends('layout.layout')

@section('content')
    <div class="container">
        <h1 class="page-header">
            Annuler une reservation client
        </h1>

        <div class="row rowSelectClient">
            <!-- Menu de selection du client -->

            <label for="select_client" class="col-md-4 control-label">Choisissez un client : </label>
            <div class="col-md-6">
                <select id="select_client" class="form-control" name="select_clientselect_client">
                    <option value="default">Selectionner un client</option>
                    @foreach($utilisateurValide as $client)
                        <option value={{ $client->id_utilisateur }}>{{ $client->email }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <hr>
    </div>
@endsection