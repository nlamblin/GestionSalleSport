@extends('layout.layout')

@section('content')
    <div class="container">
        <h1 class="page-header">
            Prendre une reservation pour un client
        </h1>

		<div class="row rowSelectClient">
		<!-- Menu de selection du client -->

			<label for="select_client_reservation" class="col-md-4 control-label">Choisissez un client : </label>
			<div class="col-md-6">
				<select id="select_client_reservation" class="form-control" name="select_client_reservation">
					<option value="default">Selectionner un client</option>
					@foreach($utilisateurValide as $client)
						<option value={{ $client->id_utilisateur }}>{{ $client->email }}</option>
					@endforeach
				</select>
			</div>
		</div>

		<div class="row row_select_activite_reservation_client">
		<!-- Menu de selection de l'activite -->

			<label for="select_activite_reservation_client" class="col-md-4 control-label">Choisissez une activité : </label>
			<div class="col-md-6">
				<select id="select_activite_reservation_client" class="form-control" name="select_activite_reservation_client">
					<option value="default">Selectionner une activité</option>
					@foreach($listeActivites as $activite)
						<option value={{ $activite->id_activite }}>{{ $activite->nom_activite }}</option>
					@endforeach
				</select>
			</div>
		</div>
    </div>
@endsection