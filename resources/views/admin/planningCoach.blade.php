@extends('layout.layout')

@section('content')
    <div class="container">
        <h1 class="page-header">
            Planning des coachs
        </h1>

        <div class="row rowSelectCoach">
            <!-- Menu de selection du coach -->

            <label for="select_coach" class="col-md-4 control-label">Choisissez un coach : </label>
            <div class="col-md-6">
                <select id="select_coach" class="form-control" name="select_coach">
                    <option value="default">Selectionner un coach</option>
                    @foreach($listecoach as $coach)
                        <option value={{ $coach->id_utilisateur }}>{{ $coach->email }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <hr>
    </div>
@endsection