@extends('base')

@section('pageTitle', 'Gestión de permisos')

@section('content')
    <div id="lista_permisos">
        <h2> Gestión de Permisos </h2>

        <div>
            @include('flash-message')
            @yield('content')
        </div>

        <table class="table">
            <tr>
                <th>Email</th>
                <th>Rol</th>
            </tr>
            @if(!empty($users) && !empty($groups))
                @foreach($users as $index => $user)
                    <tr>
                        <td>{{ucfirst($user->email)}}</td>
                        <td>
                            <form method="get"
                                  action="{{route('setPermissions')}}">
                                <select id="group_id" name="group_id">
                                    @foreach($groups as $group)
                                        @if($group->id == $userGroups[$index])
                                            <option
                                                value="{{$group->id}}" selected>{{ucfirst($group->name)}}</option>
                                        @else
                                            <option
                                                value="{{$group->id}}">{{ucfirst($group->name)}}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <input type='hidden' id='user_id' name='user_id' value='{{$user->id}}'>
                                <button type="submit">Guardar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endif
        </table>
    </div>
@endsection
