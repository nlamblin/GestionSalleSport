@extends('layout.layout')

@section('css', asset('css/home.css'))

@section('content')
    <div class="container">
        <h1 class="page-header">
            Séances
        </h1>

        <div class="row rowSelectActivite">
            <!-- Menu de selection de l'activite -->

            <label for="select_activite" class="col-md-4 control-label">Choisissez une activité : </label>
            <div class="col-md-6">
                <select id="select_activite" class="form-control" name="select_activite">
                    @foreach($listeActivites as $activite)
                        <option value={{ $activite->id_activite }}>{{ $activite->nom_activite }}</option>
                    @endforeach
                        <option value='4'>Activité 4</option>
                        <option value='2'>Activité 2</option>
                        <option value='3'>Activité 3</option>
                </select>
            </div>

        </div>
        
        <!-- Pagination -->
        <div class="row text-center">
            <div class="col-lg-12">
                <ul class="pagination">
                    <li>
                        <a href="#">&laquo;</a>
                    </li>
                    <li class="active">
                        <a href="#">1</a>
                    </li>
                    <li>
                        <a href="#">2</a>
                    </li>
                    <li>
                        <a href="#">3</a>
                    </li>
                    <li>
                        <a href="#">4</a>
                    </li>
                    <li>
                        <a href="#">5</a>
                    </li>
                    <li>
                        <a href="#">&raquo;</a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- /.row -->
        <hr>
    </div>

@endsection