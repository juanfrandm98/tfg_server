<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use App\Models\ExperienceResult;
use App\Models\Measure;
use DB;
use Illuminate\Support\Facades\Storage;

class ExperienceResultsController extends Controller
{

    /**
     * Creates a new Experience's Result in the DB.
     *
     * @param Request $request - json containing necessary data.
     * @return Request[] - operation's result.
     */
    public function newExperienceResult(Request $request)
    {

        $data = json_decode($request->get('json'), true);

        $newResultData = array(
            'userID' => $data['userID'],
            'experienceID' => $data['experienceID'],
            'userEmotion' => $data['userEmotion'],
            'startTime' => $data['startTime'],
            'frequency' => $data['frequency'],
            'valence' => $data['valence'],
            'arousal' => $data['arousal'],
            'dominance' => $data['dominance']
        );

        $newResult = new ExperienceResult($newResultData);
        $newResult->save();

        return response('Sus datos han sido registrados correctamente.', 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Add results to an existing Experience's Result.
     *
     * @param Request $request - json containing necessary data.
     * @return Request[] - operation's result.
     */
    public function addExperienceResults(Request $request)
    {
        $jsonParams = $request->get('json');
        $message = 'No ha sido posible encontrar sus datos.';
        $statusCode = 409;

        if ($jsonParams) {
            $newResults = json_decode($jsonParams, true);
            //$experience = DB::table('experience_results')->where('username', $newResults['username'])->where('startTime', $newResults['startTime'])->first();
            $experience = ExperienceResult::where('userID', $newResults['userID'])->where('startTime', $newResults['startTime'])->first();

            if ($experience) {
                $message = 'Sus datos han sido actualizados correctamente.';
                $statusCode = 200;

                if (is_null($experience->results)) {
                    $experience->results = json_decode($newResults['results'], true);
                } else {
                    $oldArray = $experience->results;
                    $newArray = json_decode($newResults['results'], true);
                    $experience->results = array_merge($oldArray, $newArray);
                }

                $experience->save();
            }
        }

        return response($message, $statusCode)->header('Content-Type', 'text/plain');
    }

    /**
     *
     */
    private function calculateExperienceResults($expRes)
    {
        $experienceMeasure = Measure::where('experienceResultID', $expRes->id)->first();

        if (!$experienceMeasure) {
            $results = $expRes->results;
            $fs = $expRes->frequency;

            $command = "Rscript ./resources/r_scripts/filtroSplinBatch.R ${fs}";

            $parsedResults = implode("\n", $results);
            Storage::disk('local')->put('results.txt', $parsedResults);

            // sed -i 's/|/\n/g'         (Pasar al formato del nuevo script)

            $salida = shell_exec($command);

            try {
                $measure = json_decode(Storage::disk('local')->get('results.txt'), true);

                $newMeasureData = array(
                    'experienceResultID' => $expRes->id,
                    'bpm' => $measure['bpm'],
                    'ibi' => $measure['ibi'],
                    'sdnn' => $measure['sdnn'],
                    'sdsd' => $measure['sdsd'],
                    'rmssd' => $measure['rmssd'],
                    'pnn20' => $measure['pnn20'],
                    'pnn50' => $measure['pnn50'],
                );

                $newMeasure = new Measure($newMeasureData);
                $newMeasure->save();
            } catch (FileNotFoundException $e) {
            }

            Storage::disk('local')->delete('results.txt');
        }
    }

    public function getExperienceResultsPage(Request $request)
    {
        $userID = session('userID');

        if (isset($userID)) {
            $results = ExperienceResult::all();

            $measures = [];
            foreach ($results as $result) {
                $this->calculateExperienceResults($result);
                $experienceMeasure = Measure::where('experienceResultID', $result->id)->first();
                array_push($measures, $experienceMeasure);
            }

            $vars = compact(['results', 'measures']);
            return view('results', $vars);
        } else {
            return redirect()->route('main');
        }
    }
}
