<header>
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ url('/') }}">Fit' Miage</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    @if (Auth::guest())
                        <li>
                            <a href="{{ url('/login') }}"><span class="glyphicon glyphicon-log-in"></span> Se connecter</a>
                        </li>
                        <li>
                            <a href="{{ url('/register') }}"><span class="glyphicon glyphicon-user"></span> S'inscrire</a>
                        </li>
                    @else
                        <li>
                            <a href="{{ url('/seances') }}"><span class="glyphicon glyphicon-basketball"></span>Réserver une séance</a>
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" id="dropdownMesSeances" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                Mes séances
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMesSeances">
                                <li><a href="#">Mes séances à venir</a></li>
                                <li><a href="#">Mes séances passées</a></li>
                            </ul>
                        </li>

                        <li class="dropdown">
                            <a class="dropdown-toggle" id="dropdownMonCompte" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                Mon compte
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMonCompte">
                                <li><a href="#"><span class="glyphicon glyphicon-user"></span> Mon compte</a></li>
                                <li><a href="{{ url('/logout') }}"><span class="glyphicon glyphicon-log-out"></span> Déconnexion</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>
</header>