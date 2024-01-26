<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class FavouriteShows extends Controller
{
    public function addFavouriteMovie(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthenticated User.'], 400);
            }

            $movie = $request->all();
            if (!isset($movie['id'])) {
                return response()->json(['error' => 'The movie must have an "id" key.'], 400);
            }

            $currentFavouriteMovies = $user->favouriteMovies ? json_decode($user->favouriteMovies, true) : [];
            $currentFavouriteMovies[$movie['id']] = $movie;

            $user->update(['favouriteMovies' => json_encode($currentFavouriteMovies)]);

            return response()->json(['message' => 'Movie added to favourites successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function addFavouriteSerie(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthenticated User.'], 400);
            }

            $serie = $request->all();
            if (!isset($serie['id'])) {
                return response()->json(['error' => 'The serie must have an "id" key.'], 400);
            }

            $currentFavouriteSeries = $user->favouriteSeries ? json_decode($user->favouriteSeries, true) : [];
            $currentFavouriteSeries[$serie['id']] = $serie;

            $user->update(['favouriteSeries' => json_encode($currentFavouriteSeries)]);

            return response()->json(['message' => 'Serie added to favourites successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function removeMovieFromFavourites(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthenticated User.'], 400);
            }

            $movieId = $request->movieId;
            $currentFavouriteMovies = $user->favouriteMovies ? json_decode($user->favouriteMovies, true) : [];

            if (isset($currentFavouriteMovies[$movieId])) {
                unset($currentFavouriteMovies[$movieId]);

                $user->update(['favouriteMovies' => json_encode($currentFavouriteMovies)]);

                return response()->json(['message' => 'Movie removed from favourites successfully.']);
            }

            return response()->json(['error' => 'Movie not found in favourites.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function removeSerieFromFavourites(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthenticated User.'], 400);
            }

            $serieId = $request->serieId;
            $currentFavouriteSeries = $user->favouriteSeries ? json_decode($user->favouriteSeries, true) : [];

            if (isset($currentFavouriteSeries[$serieId])) {
                unset($currentFavouriteSeries[$serieId]);

                $user->update(['favouriteSeries' => json_encode($currentFavouriteSeries)]);

                return response()->json(['message' => 'Serie removed from favourites successfully.']);
            }

            return response()->json(['error' => 'Serie not found in favourites.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showFavourites(Request $request) 
    {
        try {
            $user = auth()->user();
    
            if (!$user) {
                return response()->json(['error' => 'Unauthenticated User.'], 400);
            }
    
            $moviesPerPage = 5;
            $seriesPerPage = 5;
            $moviePage = $request->get('moviePage', 1);
            $seriesPage = $request->get('seriesPage', 1);
            
            $favouriteMovies = $this->paginateFavourites(json_decode($user->favouriteMovies, true), $moviesPerPage, $moviePage);
            $favouriteMoviesData = $favouriteMovies->items();

            $favouriteSeries = $this->paginateFavourites(json_decode($user->favouriteSeries, true), $seriesPerPage, $seriesPage);
            $favouriteSeriesData = $favouriteSeries->items();

            $movieTotalPages = $favouriteMovies->lastPage();
            $seriesTotalPages = $favouriteSeries->lastPage();

            return response()->json([
                'movieTotalPages' => $movieTotalPages,
                'currentMoviePage'=> $moviePage,
                'movies' => $favouriteMoviesData,
                'seriesTotalPages' => $seriesTotalPages,
                'currentSeriePage'=> $seriesPage,
                'series' => $favouriteSeriesData,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    private function paginateFavourites($favourites, $perPage, $currentPage)
    {
        $currentPageItems = array_slice($favourites, ($currentPage - 1) * $perPage, $perPage);
        return new LengthAwarePaginator($currentPageItems, count($favourites), $perPage, $currentPage);
    }
}
