@extends('layout.layout')

@section('content')
    <div class="container">
        <h1 class="page-header">
            Archivage
        </h1>

        <div class="row">
            <div class="col-lg-12">

                @if(session()->has('message'))
                    <div class="alert alert-success">
                        {{ session()->get('message') }}
                    </div>
                @endif

                @if(session()->has('messageDanger'))
                    <div class="alert alert-danger">
                        {{ session()->get('messageDanger') }}
                    </div>
                @endif

                <p>L'archivage permet de sauvegarder les séances qui ont été effectuées ainsi que les réservations passées des clients internes et externes.</p>

                <form method="POST" action="{{ route('admin/archiver') }}">
                    {{ csrf_field()}}

                    <button type="submit" class="btn btn-primary">
                        Lancer l'archivage des séances et des réservations
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection