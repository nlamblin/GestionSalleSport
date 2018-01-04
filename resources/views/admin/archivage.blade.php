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

                <p>MESSAGE EXPLICATIF DE L'ARCHIVAGE</p>

                <form method="POST" action="{{ route('admin/archiverSeance') }}">
                    {{ csrf_field()}}

                    <button type="submit" class="btn btn-primary">
                        Lancer l'archivage des séances
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection