<?php

namespace App\Http\Controllers;

use App\Models\UserGroup;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{

    public function registerUser(Request $request)
    {
        $jsonParams = $request->get('json');
        $message = 'Error en los datos enviados.';
        $statusCode = 409;

        if ($jsonParams) {
            $newUser = json_decode($jsonParams, true);

            if (User::where('email', $newUser['email'])->first()) {
                $message = 'Ya existe un usuario con ese email.';
            } else {

                if (strlen($newUser['password']) < 8) {
                    $message = 'Su contraseña debe tener, al menos, 8 caracteres.';
                } else {
                    $message = 'Usuario registrado correctamente.';
                    $statusCode = 200;

                    $newUserData = array(
                        'email' => $newUser['email'],
                        'password' => Hash::make($newUser['password']),
                        'name' => $newUser['name'],
                        'surname' => $newUser['surname'],
                        'birthday' => $newUser['birthday'],
                        'gender' => $newUser['gender']
                    );

                    $newDBEntrance = new User($newUserData);
                    $newDBEntrance->save();

                    $user = User::where('email', $newUserData['email'])->first();

                    return response()->json([
                        'message' => $message,
                        'email' => $user->email,
                        'id' => $user->id,
                    ], $statusCode);
                }

            }
        }

        return response()->json([
            'message' => $message,
        ], $statusCode);
    }

    public function simpleRegisterUser(Request $request)
    {
        $jsonParams = $request->get('json');
        $message = 'Error en los datos enviados.';
        $statusCode = 409;

        if ($jsonParams) {
            $newUser = json_decode($jsonParams, true);

            if (User::where('email', $newUser['email'])->first()) {
                $message = 'Ya existe un usuario con ese email.';
            } else {

                if (strlen($newUser['password']) < 8) {
                    $message = 'Su contraseña debe tener, al menos, 8 caracteres.';
                } else {
                    $message = 'Usuario registrado correctamente.';
                    $statusCode = 200;

                    $newUserData = array(
                        'email' => $newUser['email'],
                        'password' => Hash::make($newUser['password']),
                    );

                    $newDBEntrance = new User($newUserData);
                    $newDBEntrance->save();

                    $user = User::where('email', $newUserData['email'])->first();

                    $newUserGroupData = array(
                        'userID' => $user->id,
                        'groupID' => 1,
                    );

                    $newDBEntrance = new UserGroup($newUserGroupData);
                    $newDBEntrance->save();

                    return response()->json([
                        'message' => $message,
                        'email' => $user->email,
                        'id' => $user->id,
                    ], $statusCode);
                }

            }
        }

        return response()->json([
            'message' => $message,
        ], $statusCode);
    }

    public function loginUser(Request $request)
    {
        $message = 'Error en la recepción de los datos.';
        $statusCode = 409;

        $jsonParams = $request->get('json');
        $email = $request->get('email');
        $pswd = null;
        $user = null;

        if ($jsonParams) {
            $userLogin = json_decode($jsonParams, true);
            $user = User::where('email', $userLogin['email'])->first();
            $pswd = $userLogin['password'];
            $message = 'No existe ningún usuario con ese email.';
        } else if ($email) {
            $user = User::where('email', $email)->first();
            $pswd = $request->get('password');
            $message = 'No existe ningún usuario con ese email.';
        }

        if ($user) {
            if (Hash::check($pswd, $user->password)) {
                $message = 'Login correcto';
                $statusCode = 200;

                return response()->json([
                    'message' => $message,
                    'email' => $user->email,
                    'id' => $user->id,
                ], $statusCode);
            } else {
                $message = 'Contraseña incorrecta.';
            }
        }

        return response()->json([
            'message' => $message,
        ], $statusCode);


    }

    public function getUserGroup(int $userID)
    {
        $userGroup = UserGroup::where('userID', $userID)->first();
        return $userGroup->groupID;
    }

    public function loginUserWeb(Request $request)
    {
        $response = $this->loginUser($request);

        $statusCode = $response->status();
        $validUser = json_decode($response->getContent(), true);

        if ($validUser != null) {
            if ($statusCode == 200) {
                $userID = $validUser['id'];
                $groupID = $this->getUserGroup($userID);

                if($groupID != 1) {
                    session(['userID' => $userID]);
                    session(['userGroup' => $groupID]);
                    return redirect()->route('menu');
                } else {
                    return back()->with(['warning' => 'No tiene los permisos necesarios para entrar en la web.']);
                }

            } else {
                return back()->with(['warning' => $validUser['message']]);
            }
        }

        return back()->with('warning', json_last_error_msg());

    }

    public function logOutUserWeb(Request $request)
    {
        session()->forget('userID');
        session()->forget('userGroup');
        return redirect()->route('main');
    }

    public function changePassword(Request $request)
    {
        $jsonParams = $request->get('json');
        $message = 'Error en la recepción de los datos.';
        $statusCode = 409;

        if ($jsonParams) {
            $userData = json_decode($jsonParams, true);
            $user = User::where('id', $userData['id'])->first();

            if ($user) {
                if (Hash::check($userData['actualPassword'], $user->password)) {
                    if ($userData['actualPassword'] == $userData['newPassword']) {
                        $message = 'La nueva contraseña debe ser distinta a la anterior.';
                    } else {
                        if (strlen($userData['newPassword']) < 8) {
                            $message = 'Su contraseña debe tener, al menos, 8 caracteres.';
                        } else {
                            $message = 'Contraseña cambiada con exito';
                            $statusCode = 200;

                            $user->password = Hash::make($userData['newPassword']);
                            $user->save();
                        }
                    }
                } else {
                    $message = 'Contraseña actual incorrecta.';
                }
            } else {
                $message = 'No existe el usuario solicitado.';
            }
        }

        return response($message, $statusCode)->header('Content-Type', 'text/plain');
    }

    public function getUserData(Request $request)
    {
        $jsonParams = $request->get('json');
        $message = 'Error en la recepción de los datos.';
        $statusCode = 409;

        if ($jsonParams) {
            $userID = json_decode($jsonParams, true);
            $user = User::where('id', $userID['id'])->first();

            if ($user) {
                $message = 'Usuario correcto.';
                $statusCode = 200;

                return response()->json([
                    'message' => $message,
                    'name' => $user->name,
                    'surname' => $user->surname,
                    'birthday' => $user->birthday,
                    'gender' => $user->gender,
                    'country' => $user->country,
                ], $statusCode);
            } else {
                $message = 'No existe el usuario solicitado.';
            }
        }

        return response()->json([
            'message' => $message,
        ], $statusCode);
    }

    public function setUserData(Request $request)
    {
        $jsonParams = $request->get('json');
        $message = 'Error en la recepción de los datos.';
        $statusCode = 409;

        if ($jsonParams) {
            $userData = json_decode($jsonParams, true);
            $user = User::where('id', $userData['id'])->first();

            if ($user) {
                $message = 'Datos del usuario modificados correctamente.';
                $statusCode = 200;

                if ($userData['name'] != '')
                    $user->name = $userData['name'];
                else
                    $user->name = null;

                if ($userData['surname'] != '')
                    $user->surname = $userData['surname'];
                else
                    $user->surname = null;

                if ($userData['birthday'] != '')
                    $user->birthday = $userData['birthday'];
                else
                    $user->birthday = null;

                if ($userData['gender'] != '')
                    $user->gender = $userData['gender'];
                else
                    $user->gender = null;

                if ($userData['country'] != '')
                    $user->country = $userData['country'];
                else
                    $user->country = null;

                $user->save();
            } else {
                $message = 'No existe el usuario solicitado.';
            }
        }

        return response($message, $statusCode)->header('Content-Type', 'text/plain');
    }

    public function goToMenu() {
        $userID = session('userID');

        if(isset($userID)) return view('menu');
        else return redirect()->route('main');
    }

}
