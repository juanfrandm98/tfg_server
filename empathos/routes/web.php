<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ExperienceResultsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\TestsController;
use App\Http\Controllers\GroupsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
Route::get('/', function () {
    return view('welcome');
});*/

/**
 * USER ROUTES
 */
Route::get('registerUser', [UsersController::class, 'simpleRegisterUser']);
Route::get('loginUser', [UsersController::class, 'loginUser']);
Route::get('loginUserWeb', [UsersController::class, 'loginUserWeb'])->name('loginUser');
Route::get('logOutUserWeb', [UsersController::class, 'logOutUserWeb'])->name('logOutUser');
Route::get('getUserData', [UsersController::class, 'getUserData']);
Route::get('setUserData', [UsersController::class, 'setUserData']);
Route::get('changePassword', [UsersController::class, 'changePassword']);

/**
 * TEST ROUTES
 */
Route::get('addTest', [TestsController::class, 'addTest']);
Route::get('getTestsList', [TestsController::class, 'getTestsList']);
Route::get('editTest', [TestsController::class, 'editTest'])->name('editTest');
Route::get('deleteTest', [TestsController::class, 'deleteTest'])->name('deleteTest');
Route::get('changeActiveTest', [TestsController::class, 'changeActiveTest'])->name('changeActive');

/**
 * EXPERIENCE RESULTS ROUTES
 */
Route::get('newExperienceResult', [ExperienceResultsController::class, 'newExperienceResult']);
Route::get('addExperienceResults', [ExperienceResultsController::class, 'addExperienceResults']);

/**
 * GROUPS ROUTES
 */
Route::get('addGroup', [GroupsController::class, 'addGroup']);
Route::get('setUserPermissions', [GroupsController::class, 'setUserPermissions'])->name('setPermissions');

/**
 * WEBPAGE ROUTES
 */
Route::get('/', function() {
    return view('loginPage');
})->name('main');

Route::get('menuPage', [UsersController::class, 'goToMenu'])->name('menu');
Route::get('permissionsPage', [GroupsController::class, 'permissionsPage'])->name('permissions');
Route::get('testListPage', [TestsController::class, 'getTestsListWeb'])->name('testList');
Route::get('goToNewTest', [TestsController::class, 'goToNewTest'])->name('newTestPage');
Route::get('goToEditTest', [TestsController::class, 'goToEditTest'])->name('editTestPage');
Route::get('getExperienceResultsPage', [ExperienceResultsController::class, 'getExperienceResultsPage'])->name('results');


