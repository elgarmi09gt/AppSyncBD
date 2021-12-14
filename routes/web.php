<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EtablissementController;
use App\Http\Controllers\TypeSectionController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\GradeFormationController;
use App\Http\Controllers\DomaineController;
use App\Http\Controllers\MentionController;
use App\Http\Controllers\SpecialiteController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\EcoleDoctoraleController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\NiveauFormationController;


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

Route::get('/', function () {
    return view('welcome');
})->name('index');

Route::get('/etab', [EtablissementController::class, 'synchEtablissement'])->name('syncEtab');
Route::get('/type', [TypeSectionController::class, 'synchTypeSection'])->name('syncType');
Route::get('/dept', [DepartementController::class, 'synchDepartement'])->name('syncDept');
Route::get('/grade', [GradeFormationController::class, 'synchGradeFormation'])->name('syncGrade');
Route::get('/domaine', [DomaineController::class, 'synchDomaine'])->name('syncDomaine');
Route::get('/mention', [MentionController::class, 'syncMention'])->name('syncMention');
Route::get('/specialite', [SpecialiteController::class, 'syncSpecialite'])->name('syncSpecialite');
Route::get('/option', [OptionController::class, 'syncOption'])->name('syncOption');
Route::get('/ecoleDoc', [EcoleDoctoraleController::class, 'syncEcoleDoctorale'])->name('syncEcoleDoc');
Route::get('/form', [FormationController::class, 'syncFormation'])->name('syncForm');
Route::get('/nivform', [NiveauFormationController::class, 'syncNiveauFormation'])->name('syncNivForm');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
