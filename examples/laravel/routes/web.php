<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web routes for the CAS sample
|--------------------------------------------------------------------------
|
| This sample supports TWO ways to sign in, sharing ONE app session:
|
|   LOCAL username/password (SQLite-backed):
|     GET  /login     -> render the local login form
|     POST /login     -> validate against SQLite + start session (browser),
|                        OR serve the CAS link-validation contract (JSON) when
|                        the body carries "client_validation"
|
|   CAS single sign-on (unchanged, via the package's CasClient facade):
|     GET  /cas/login -> redirect to the CAS server's /sso/login
|     GET  /callback  -> validate the returned token via the package, start session
|
|   Shared:
|     GET  /          -> show the authenticated user (local OR CAS), or login options
|     POST /logout    -> clear the session
|
| All delegate to App\Http\Controllers\AuthController. The CAS half uses the
| package's CasClient facade exactly as the package README demonstrates.
|
| NOTE: the package ALSO auto-registers its own routes under the "cas" prefix.
| We disable those via CAS_ROUTES_ENABLED=false so the routes below are the
| single, clear entry point. Either approach works; see the README.
|
*/

Route::get('/', [AuthController::class, 'home'])->name('home');

// Local username/password auth.
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'localLogin'])->name('login.post');

// CAS single sign-on.
Route::get('/cas/login', [AuthController::class, 'casLogin'])->name('cas.login');
Route::get('/callback', [AuthController::class, 'callback'])->name('callback');

// Shared logout.
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
