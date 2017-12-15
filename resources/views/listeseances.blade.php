@extends('layout.layout')

@section('css', asset('css/home.css'))


@section('content')

    <!-- Header Carousel -->
    <header id="myCarousel" class="carousel slide">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1"></li>
            <li data-target="#myCarousel" data-slide-to="2"></li>
        </ol>

        <!-- Wrapper for slides -->
        <div class="carousel-inner">
            <div class="item active">
                <div class="fill" style="background-image:url('http://placehold.it/1900x1080&text=Slide One');"></div>
                <div class="carousel-caption">
                    <h2>Caption 1</h2>
                </div>
            </div>
            <div class="item">
                <div class="fill" style="background-image:url('http://placehold.it/1900x1080&text=Slide Two');"></div>
                <div class="carousel-caption">
                    <h2>Caption 2</h2>
                </div>
            </div>
            <div class="item">
                <div class="fill" style="background-image:url('http://placehold.it/1900x1080&text=Slide Three');"></div>
                <div class="carousel-caption">
                    <h2>Caption 3</h2>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <a class="left carousel-control" href="#myCarousel" data-slide="prev">
            <span class="icon-prev"></span>
        </a>
        <a class="right carousel-control" href="#myCarousel" data-slide="next">
            <span class="icon-next"></span>
        </a>
    
    

           <div class="row">
            <!-- Menu de selection de l'activite -->

            <label for="select_activite" class="col-md-4 control-label">Choisissez une activité : </label>
                    <div class="col-md-6">
                        <select id="select_activite" class="form-control" name="select_activite">
            @foreach($listeactivite as $value)
                            <option value=$value.id_activite>{{$value->nom_activite}}</option>
            @endforeach             
                        </select>
                </div>


           </div>

       </header>
        <div class="container">
       <!-- Project One -->
       @foreach($listeseance as $value)
       <!-- Project one -->
        <div class="row">
            <div class="col-md-7">
                <a href="portfolio-item.html">
                    <img class="img-responsive img-hover" src="http://placehold.it/700x300" alt="">
                </a>
            </div>
            <div class="col-md-5">
                <h3>Activité : {{$value->nom_activite}} </h3>
                <h4>Séance : {{$value->type_seance}}</h4>
                <div class = "row">
                    <span> 
                        Niveau : {{$value->niveau_seance}}
                    </span>
                </div>
                <div class = "row">
                    <span> 
                        @if($value->type_seance="individuelle")
                        Coach personnel : 
                            @if($value->avec_coach=true) 
                            disponible 
                            @else
                            indisponible
                            @endif
                        @else
                        Coach collectif :
                        @if($value->avec_coach=true) 
                            disponible 
                            @else
                            indisponible
                            @endif
                        @endif
                    </span>
                </div>
                <div class = "row">
                    <span> 
                        Date : {{$value->date_seance}} - Heure : {{$value->heure_seance}}
                    </span>
                </div>
            </div>
        </div>
        <!-- /.row -->

        <hr>
        @endforeach     
        
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