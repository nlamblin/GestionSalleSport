<nav class="navbar navbar-inverse">
    <div class="container-fluid">
        <ul class="nav navbar-nav">
            <li class="active"><a href="{{ url('/') }}">Home</a></li>
            {{--<li><a href="#">Page 1</a></li>
            <li><a href="#">Page 2</a></li>--}}
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="{{ url('/login')  }}"><span class="glyphicon glyphicon-log-in"></span> Sign In</a></li>
            <li><a href="{{ url('/register') }}"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
        </ul>
    </div>
</nav>