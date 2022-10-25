@extends('base')

@section('pageTitle', 'Menú Principal')

@section('content')
    <div id="menu_principal">
        <h2> Menú Principal </h2>

        <div id="opciones_menu_principal">
            <a href="{{ route('testList') }}"> Lista de pruebas </a>
            <a href="{{ route('results') }}"> Resultados </a>
            @if(session('userGroup') == 3)
                <a href="{{ route('permissions') }}"> Gestionar permisos </a>
            @endif
            <a href="{{ route('logOutUser') }}"> Cerrar sesión</a>
        </div>
    </div>
@endsection
