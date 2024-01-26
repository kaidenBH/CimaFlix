<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShowsController;
use App\Http\Controllers\FavouriteShows;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('/signUp', [UserController::class, 'signup']);
    Route::post('/signIn', [UserController::class, 'signin']);
});

Route::group(['prefix' => 'show'], function () {
    Route::get('/movieList', [ShowsController::class, 'getMovies']);
    Route::get('/serieList', [ShowsController::class, 'getSeries']);

    Route::get('/searchMovies', [ShowsController::class, 'searchMovies']);
    Route::get('/searchSeries', [ShowsController::class, 'searchSeries']);

    Route::get('/movie/{id}', [ShowsController::class, 'movieDetails']);
    Route::get('/serie/{id}', [ShowsController::class, 'serieDetails']);

    Route::get('/movieTrailers/{id}', [ShowsController::class, 'movieTrailers']);
    Route::get('/serieTrailers/{id}', [ShowsController::class, 'serieTrailers']);

    Route::middleware('auth:sanctum')->put('/addMovieToFavourites', [FavouriteShows::class, 'addFavouriteMovie']);
    Route::middleware('auth:sanctum')->put('/addSerieToFavourites', [FavouriteShows::class, 'addFavouriteSerie']);

    Route::middleware('auth:sanctum')->put('/removeMovieToFavourites', [FavouriteShows::class, 'removeMovieFromFavourites']);
    Route::middleware('auth:sanctum')->put('/removeSerieToFavourites', [FavouriteShows::class, 'removeSerieFromFavourites']);

    Route::middleware('auth:sanctum')->get('/favourites', [FavouriteShows::class, 'showFavourites']);
});
