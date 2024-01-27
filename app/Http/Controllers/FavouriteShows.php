<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\UserService;

class FavouriteShows extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    
    public function addFavouriteMovie(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthenticated User.'], 400);
            }

            $movie = $request->all();
            $currentFavouriteMovies = json_decode($user->favouriteMovies, true) ?? [];

            $result = $this->userService->addFavourite($user, $currentFavouriteMovies, $movie, 'favouriteMovies');

            return response()->json($result);
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
            $currentFavouriteSeries = json_decode($user->favouriteSeries, true) ?? [];

            $result = $this->userService->addFavourite($user, $currentFavouriteSeries, $serie, 'favouriteSeries');

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function removeMovieFromFavourites(Request $request, $itemId)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthenticated User.'], 400);
            }

            $currentFavouriteMovies = json_decode($user->favouriteMovies, true) ?? [];
            $result = $this->userService->removeFavourite($user, $currentFavouriteMovies, $itemId, 'favouriteMovies');

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function removeSerieFromFavourites(Request $request, $itemId)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthenticated User.'], 400);
            }

            $currentFavouriteSeries = json_decode($user->favouriteSeries, true) ?? [];
            $result = $this->userService->removeFavourite($user, $currentFavouriteSeries, $itemId, 'favouriteSeries');

            return response()->json($result);
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
