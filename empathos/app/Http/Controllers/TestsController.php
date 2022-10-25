<?php

namespace App\Http\Controllers;

use App\Models\Test;
use Illuminate\Http\Request;

class TestsController extends Controller
{

    public function addTest(Request $request)
    {
        $jsonParams = $request->get('json');
        $message = 'Error en los datos enviados.';
        $statusCode = 409;

        if ($jsonParams) {
            $newTest = json_decode($jsonParams, true);

            $newTestData = array(
                'title' => $newTest['title'],
                'description' => $newTest['description'],
                'duration' => $newTest['duration'],
                'resultStart' => $newTest['resultStart'],
                'resultDuration' => $newTest['resultDuration'],
            );

            $newDBEntrance = new Test($newTestData);
            $newDBEntrance->save();

            if ($test = Test::where('title', $newTest['title'])->where('description', $newTest['description'])->first()) {
                $message = 'Prueba introducida (identificador' . $test->id . ").";
                $statusCode = 200;
            } else {
                $message = 'Error al introducir los datos en la BD.';
            }
        }

        return response($message, $statusCode)->header('Content-Type', 'text/plain');
    }

    public function getTestsList(Request $request)
    {
        //$tests = Test::all();

        $testsList = Test::where('active', true)->get()->toJson();


        return response($testsList, 200)->header('Content-Type', 'application/json');
    }

    public function getTestsListWeb(Request $request)
    {
        $userID = session('userID');

        if (isset($userID)) {
            $testList = Test::all();

            return view('test-list', ['testList' => $testList]);
        } else {
            return redirect()->route('main');
        }
    }

    public function goToNewTest(Request $request)
    {
        $userID = session('userID');

        if (isset($userID)) {
            return view('edit-test');
        } else {
            return redirect()->route('main');
        }
    }

    public function goToEditTest(Request $request)
    {
        $userID = session('userID');

        if (isset($userID)) {
            $testID = $request->get('test_id');

            if ($testID) {
                $test = Test::where('id', $testID)->first();

                return view('edit-test', ['initialData' => $test]);
            } else {
                return back()->with(['warning' => 'No se ha podido acceder a los datos de la Prueba.']);
            }
        } else {
            return redirect()->route('main');
        }
    }

    private function checkTestParams(Request $request)
    {
        $title = $request->get('title');
        $description = $request->get('description');
        $duration = $request->get('duration');
        $resultStart = $request->get('resultStart');
        $resultDuration = $request->get('resultDuration');

        if (isset($title) && isset($description) && isset($duration) && isset($resultStart) && isset($resultDuration)) {
            if (is_numeric($duration) && is_numeric($resultStart) && is_numeric($resultDuration)) {
                return 'OK';
            } else {
                return 'Los tres últimos valores deben ser números';
            }
        } else {
            return 'Debe rellenar todos los campos.';
        }
    }

    public function editTest(Request $request)
    {
        $userID = session('userID');

        if (isset($userID)) {
            $check = $this->checkTestParams($request);

            if ($check == 'OK') {
                $testID = $request->get('id');
                $title = $request->get('title');
                $description = $request->get('description');
                $duration = $request->get('duration');
                $resultStart = $request->get('resultStart');
                $resultDuration = $request->get('resultDuration');

                if (isset($testID)) {
                    if ($test = Test::where('id', $testID)->first()) {
                        $test->title = $title;
                        $test->description = $description;
                        $test->duration = $duration;
                        $test->resultStart = $resultStart;
                        $test->resultDuration = $resultDuration;

                        $test->save();

                        return redirect()->route('testList')->with(['success' => 'Prueba modificada con éxito.']);
                    } else {
                        return back()->with(['success' => 'Error al localizar la prueba.']);
                    }
                } else {
                    $newTestData = array(
                        'title' => $title,
                        'description' => $description,
                        'duration' => $duration,
                        'resultStart' => $resultStart,
                        'resultDuration' => $resultDuration,
                    );

                    $newDBEntrance = new Test($newTestData);
                    $newDBEntrance->save();

                    if ($test = Test::where('title', $title)->where('description', $description)->first()) {
                        return redirect()->route('testList')->with(['success' => 'Prueba introducida con éxito.']);
                    } else {
                        return back()->with(['success' => 'Error al introducir la prueba.']);
                    }
                }
            } else {
                return back()->with(['warning' => $check]);
            }
        } else {
            return redirect()->route('main');
        }
    }

    public function deleteTest(Request $request)
    {
        $userID = session('userID');

        if (isset($userID)) {
            $testID = $request->get('test_id');

            if (isset($testID)) {
                $test = Test::where('id', $testID)->first();

                if ($test) {
                    $test->delete();
                    $test = Test::where('id', $testID)->first();

                    if ($test) {
                        return back()->with(['warning' => 'Error al eliminar la prueba.']);
                    } else {
                        return $this->getTestsListWeb($request);
                    }
                } else {
                    return back()->with(['warning' => 'Error al localizar la prueba.']);
                }
            } else {
                return back()->with(['warning' => 'Error al leer el identificador de la prueba.']);
            }
        } else {
            return redirect()->route('main');
        }
    }

    public function changeActiveTest(Request $request)
    {
        $userID = session('userID');

        if (isset($userID)) {
            $testID = $request->get('test_id');

            if (isset($testID)) {
                $test = Test::where('id', $testID)->first();

                if ($test) {
                    $test->active = !$test->active;
                    $test->save();

                    return $this->getTestsListWeb($request);
                } else {
                    return back()->with(['warning' => 'Error al localizar la prueba.']);
                }
            } else {
                return back()->with(['warning' => 'Error al leer el identificador de la prueba.']);
            }
        } else {
            return redirect()->route('main');
        }
    }

}
