<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="/css/index.css">
</head>

<body>
    <header>
        <nav id="navbar">
            <ul>
                <li>
                    <a href="/" class="nav-link">Home</a>
                </li>
                @auth
                    <li>
                        <a href="/dashboard" class="nav-link">Profile</a>
                    </li>
                    <li>
                        <a href="/evento/novo" class="nav-link">Create Event</a>
                    </li>
                    <li>
                        <form action="/logout" method="post">
                            @csrf
                            <a href="/logout" onclick="event.preventDefault();this.closest('form').submit();" class="nav-link">Logout</a>
                        </form>
                    </li>
                @endauth
                @guest
                    <li>
                        <a href="/login" class="nav-link">Sign in</a>
                    </li>
                    <li>
                        <a href="/register" class="nav-link">Register Account</a>
                    </li>
                @endguest
            </ul>
        </nav>
    </header>
    <main id="@yield('background')">
        @if (session('msgSuccess'))
            <br>
            <div id="div-success" class="div-msg">
                {{session('msgSuccess')}}    
            </div>           
            <br>
        @endif
        @if (session('msgWarn'))
            <br>
            <div  id="div-warn" class="div-msg">
                {{session('msgWarn')}}    
            </div>           
            <br>
        @endif
        @if (session('msgDanger'))
            <br>
            <div  id="div-danger" class="div-msg">
                {{session('msgDanger')}}    
            </div>           
            <br>
        @endif
        @yield('content')
    </main>
</body>

</html>
