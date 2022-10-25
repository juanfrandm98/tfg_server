<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Request;

class GroupsController extends Controller
{
    public function addGroup(Request $request)
    {
        $jsonParams = $request->get('json');
        $message = 'Error en los datos enviados.';
        $statusCode = 409;

        if ($jsonParams) {
            $newGroup = json_decode($jsonParams, true);

            $newGroupData = array(
                'name' => $newGroup['name'],
                'description' => $newGroup['description'],
            );

            $newDBEntrance = new Group($newGroupData);
            $newDBEntrance->save();

            if ($test = Group::where('name', $newGroup['name'])->where('description', $newGroup['description'])->first()) {
                $message = 'Grupo introducida (identificador' . $test->id . ").";
                $statusCode = 200;
            } else {
                $message = 'Error al introducir los datos en la BD.';
            }
        }

        return response($message, $statusCode)->header('Content-Type', 'text/plain');
    }

    public function permissionsPage(Request $request)
    {
        $userID = session('userID');
        $userGroup = session('userGroup');

        if (isset($userID)) {
            if ($userGroup == 3) {
                $users = User::all();
                $found = false;

                for ($i = 0; $i < count($users) && !$found; $i++) {
                    if ($userID == $users[$i]->id) {
                        $found = true;
                        unset($users[$i]);
                    }
                }

                $groups = Group::all();
                $userGroupsList = UserGroup::all();

                $userGroups = [];
                foreach ($userGroupsList as $ug) {
                    array_push($userGroups, $ug->groupID);
                }

                $vars = compact(['users', 'groups', 'userGroups']);
                return view('permissions', $vars);
            } else {
                return redirect()->route('menu');
            }
        } else {
            return redirect()->route('main');
        }
    }

    private function checkChange($userID)
    {
        $admins = UserGroup::where('groupID', 3)->get();

        if (count($admins) == 1 && $admins[0]->userID == $userID) return false;

        return true;
    }

    public function setUserPermissions(Request $request)
    {
        $userID = $request->get('user_id');
        $groupID = $request->get('group_id');

        if ($userID && $groupID) {
            $userGroup = UserGroup::where('userID', $userID)->first();

            if ($userGroup) {
                $possible = $this->checkChange($userID);

                if ($possible) {
                    $userGroup->groupID = $groupID;
                    $userGroup->save();

                    return redirect()->route('permissions')->with(['success' => 'Permisos cambiados con éxito.']);
                } else {
                    return back()->with(['warning' => 'El sistema no puede quedarse sin ningún administrador.']);
                }
            } else {
                return back()->with(['warning' => 'Error al localizar el usuario seleccionado.']);
            }
        }
    }
}
