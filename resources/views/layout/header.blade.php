@php
    $userModel = \App\Models\User::class;
    $user = \Illuminate\Support\Facades\Auth::user();
@endphp

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
                    @guest
                        <li>
                            <a href="{{ url('/login') }}"><span class="glyphicon glyphicon-log-in"></span> Se connecter</a>
                        </li>
                        <li>
                            <a href="{{ url('/register') }}"><span class="glyphicon glyphicon-user"></span> S'inscrire</a>
                        </li>
                    @endguest
                    @auth
                        @if($userModel::getUser($user->id_utilisateur)->estEmploye() || $userModel::getUser($user->id_utilisateur)->estAdmin())
                            <li class="dropdown">
                                <a class="dropdown-toggle" id="dropdownAdministration" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <span class="glyphicon glyphicon-list"></span> Administration
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownAdministration">
                                    @if($userModel::getUser($user->id_utilisateur)->estAdmin())
                                        <li><a href="{{ url('admin/showCreationActivite') }}">Créer une activité</a></li>
                                    @endif

                                    <li><a href="{{ url('admin/showCreationSeance') }}">Créer une séance</a></li>

                                    @if($userModel::getUser($user->id_utilisateur)->estAdmin())
                                        <li><a href="{{ url('admin/showAjoutEmploye') }}">Ajouter un employé</a></li>
                                    @endif

                                    <li><a href="{{ url('admin/showAjoutCoach') }}">Ajouter un coach</a></li>

                                    @if($userModel::getUser($user->id_utilisateur)->estAdmin())
                                        <li><a href="{{ url('admin/archivage') }}">Archivage</a></li>
                                    @endif
                                </ul>
                            </li>


                            <li class="dropdown">
                                <a class="dropdown-toggle" id="dropdownGestionReservationClient" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <span class="glyphicon glyphicon-list"></span> Gestion réservations clients
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownGestionReservationClient">
                                    <li><a href="{{ url('admin/showReservationClient') }}">Réserver pour un client</a></li>
                                    <li><a href="{{ url('admin/showAnnulationClient') }}">Annuler pour un client</a></li>
                                </ul>
                            </li>
                        @endif

                        @if($userModel::getUser($user->id_utilisateur)->estClient())
                            <li>
                                <a href="{{ url('client/seances') }}"><span class="glyphicon glyphicon-shopping-cart"></span> Réserver une séance</a>
                            </li>

                            <li class="dropdown">
                                <a class="dropdown-toggle" id="dropdownMesSeances" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <span class="glyphicon glyphicon-list"></span> Mes séances
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMesSeances">
                                    <li><a href="{{ url('client/seancesVenir') }}">Mes séances à venir</a></li>
                                    <li><a href="{{ url('client/seancesPassees') }}">Mes séances passées</a></li>
                                </ul>
                            </li>
                        @endif
                        @if($userModel::getUser($user->id_utilisateur)->estCoach()) 
                            <li>
                                <a href=href="{{ url('coach/seancesVenir') }}"><span class="glyphicon glyphicon-list"></span> Mes séances à venir</a>
                            </li>
                        @endif

                        <li class="dropdown">
                            <a class="dropdown-toggle" id="dropdownMonCompte" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <span class="glyphicon glyphicon-user"></span> Mon compte
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMonCompte">
                                <li><a href="{{ url('/compte') }}">Mon compte</a></li>
                                <li><a href="{{ url('/logout') }}">Déconnexion</a></li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>
</header>