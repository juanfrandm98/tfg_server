<html lang="es">
<head>
    <title>Empathos - Login</title>
    <meta charset="utf-8">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
<div class="login_background">
    <div class="login">

        <img src="{{ asset('images/red-heart.png') }}">

        <h1> Proyecto Empatímetro </h1>

        <h2> LOGIN </h2>

        <div>
            @include('flash-message')
            @yield('content')
        </div>

        <form method="get" action="{{ route('loginUser') }}" class="login_form">
            <p>Email: <input type="text" name="email"/></p>
            <p>Contraseña: <input type="password" name="password"/></p>

            <p><input type="submit" class="button" value="Login">
        </form>
    </div>
</div>
</body>
</html>
