<html lang="es">
<head>
    <title>@yield('pageTitle') - Empathos</title>
    <meta charset="utf-8">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
<header>
    <div class="header">
        <img class="header-icon" src="{{ asset('images/red-heart.png') }}">

        <div class="menu-header">
            <ul>
                <li><a href="{{ route('menu') }}">Menú principal</a></li>
                <li><a href="{{ route('logOutUser') }}">Cerrar sesión</a></li>
            </ul>
        </div>
    </div>
</header>

<div class="content">
    @yield('content')
</div>
</body>
</html>
