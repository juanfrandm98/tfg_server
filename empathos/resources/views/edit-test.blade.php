@extends('base')

@if(isset($initialData))
    @section('pageTitle', 'Editar Prueba')
@else
    @section('pageTitle', 'Nueva Prueba')
@endif

@section('content')
    <div class="edit_test_zone">
        <h2>
            @if(isset($initialData))
                Editar Prueba
            @else
                Nueva Prueba
            @endif
        </h2>

        <div>
            @include('flash-message')
            @yield('content')
        </div>

        <form class="edit_test_form" method="get" action="{{route('editTest')}}">
            <label class="edit_label" for="title">Nombre de la
                prueba:</label><br>
            <input type="text" name="title" class="small_text_input"
                   @if(isset($initialData)) value="{{ucfirst($initialData->title)}}" @endif><br><br>

            <label class="edit_label" for="description">Descripción:</label><br>
            <textarea name="description" class="big_text_input">@if(isset($initialData)){{$initialData->description}}@endif</textarea><br><br>

            <label class="edit_label" for="duration">Duración total
                (segundos):</label>
            <input type="number" name="duration" class="small_text_input"
                   @if(isset($initialData)) value="{{ucfirst($initialData->duration)}}" @endif><br><br>

            <label class="edit_label" for="resultStart">Tiempo de inicio de la
                toma de medidas
                (segundos):</label>
            <input type="number" name="resultStart" class="small_text_input"
                   @if(isset($initialData)) value="{{ucfirst($initialData->resultStart)}}" @endif><br><br>

            <label class="edit_label" for="resultDuration">Duración de la toma
                de medidas
                (segundos):</label>
            <input type="number" name="resultDuration" class="small_text_input"
                   @if(isset($initialData)) value="{{ucfirst($initialData->resultDuration)}}" @endif><br><br>

            <input type='hidden' id='id' name='id'
                   @if(isset($initialData)) value='{{$initialData->id}}' @endif>

            <button class="button" type="submit"> Guardar</button>
        </form>

        <form method="get" action="{{route('testList')}}">
            <button class="button" type="submit"> Atrás</button>
        </form>
    </div>
@endsection
