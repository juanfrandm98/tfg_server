@extends('base')

@section('pageTitle', 'Resultados')

@section('content')
    <div class="results_zone">
        <h2> Lista de resultados </h2>

        <table class="table">
            <tr>
                <th class="small_td">Usuario</th>
                <th class="small_td">Prueba</th>
                <th class="small_td">Fecha de inicio</th>
                <th class="small_td">Emoción descrita</th>
                <th class="small_td">Valence</th>
                <th class="small_td">Arousal</th>
                <th class="small_td">Dominance</th>
                <th class="small_td">Results</th>
                <th class="small_td">Frecuencia</th>
                <th class="small_td">BPM</th>
                <th class="small_td">IBI</th>
                <th class="small_td">SDNN</th>
                <th class="small_td">SDSD</th>
                <th class="small_td">RMSSD</th>
                <th class="small_td">PNN20</th>
                <th class="small_td">PNN50</th>
            </tr>

            @if(isset($results))
                @foreach($results as $index => $result)
                    <tr>
                        <td class="small_th">{{ucfirst($result->userID)}}</td>
                        <td class="small_th">{{ucfirst($result->experienceID)}}</td>
                        <td class="small_th">{{ucfirst($result->startTime)}}</td>
                        <td class="small_th">{{ucfirst($result->userEmotion)}}</td>
                        <td class="small_th">{{ucfirst($result->valence)}}</td>
                        <td class="small_th">{{ucfirst($result->arousal)}}</td>
                        <td class="small_th">{{ucfirst($result->dominance)}}</td>
                        <td class="small_th">
                            <div
                                class="scrollable">{{implode(",", $result->results)}}
                            </div>
                        </td>
                        <td class="small_th">{{ucfirst($result->frequency)}}</td>
                        <td class="small_th">{{ucfirst($measures[$index]->bpm)}}</td>
                        <td class="small_th">{{ucfirst($measures[$index]->ibi)}}</td>
                        <td class="small_th">{{ucfirst($measures[$index]->sdnn)}}</td>
                        <td class="small_th">{{ucfirst($measures[$index]->sdsd)}}</td>
                        <td class="small_th">{{ucfirst($measures[$index]->rmssd)}}</td>
                        <td class="small_th">{{ucfirst($measures[$index]->pnn20)}}</td>
                        <td class="small_th">{{ucfirst($measures[$index]->pnn50)}}</td>
                    </tr>
                @endforeach
            @endif
        </table>
    </div>
@endsection
