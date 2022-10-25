@extends('base')

@section('pageTitle', 'Pruebas')

@section('content')
    <div class="test_list_zone">
        <div>
            <h2> Lista de Pruebas </h2>
        </div>

        <div>
            @include('flash-message')
            @yield('content')
        </div>

        <div class="test_list_button">
            <a class="button" href="{{route('newTestPage')}}"> Crear Prueba </a>
        </div>

        <div class="test_list_table">
            <table class="table">
                <tr>
                    <th> ID</th>
                    <th> Nombre</th>
                    <th> Descripción</th>
                    <th> Duración Total</th>
                    <th> Comienzo medidas</th>
                    <th> Duración medidas</th>
                </tr>
                @if(!empty($testList))
                    @foreach($testList as $test)
                        <tr>
                            <td>{{ucfirst($test->id)}}</td>
                            <td>{{ucfirst($test->title)}}</td>
                            <td>{{ucfirst($test->description)}}</td>
                            <td>{{ucfirst($test->duration)}}</td>
                            <td>{{ucfirst($test->resultStart)}}</td>
                            <td>{{ucfirst($test->resultDuration)}}</td>
                            <td>
                                <form method="get", action="{{route('editTestPage')}}">
                                    <input type='hidden' id='test_id' name='test_id' value='{{$test->id}}'>
                                    <button type="submit">Editar</button>
                                </form>
                                <form method="get", action="{{route('changeActive')}}">
                                    <input type='hidden' id='test_id' name='test_id' value='{{$test->id}}'>
                                    <button type="submit">
                                        @if($test->active)
                                            Desactivar
                                        @else
                                            Activar
                                        @endif
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>
    </div>
@endsection
