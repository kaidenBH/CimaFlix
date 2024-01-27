<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Favourites;
use App\Services\ShowsService;
use Illuminate\Support\Facades\Config;

class FavouriteShows extends Controller
{
    protected $showsService;

    public function __construct(ShowsService $showsService)
    {
        $this->showsService = $showsService;
    }
    
    public function addFavouriteMovie($movieId)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthenticated User.'], 400);
            }

            $favourite = Favourites::create([
                'showId' => $movieId,
                'type' => 'movie',
                'UserId' => $user->id,
            ]);
    
            return response()->json(['message' => 'Movie added to favorites successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function addFavouriteSerie($serieId)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthenticated User.'], 400);
            }

            $favourite = Favourites::create([
                'showId' => $serieId,
                'type' => 'tv',
                'UserId' => $user->id,
            ]);

            return response()->json(['message' => 'Serie added to favorites successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function removeMovieFromFavourites($movieId)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthenticated User.'], 400);
            }   

            Favourites::where('UserId', $user->id)
                ->where('showId', $movieId)
                ->where('type', 'movie')
                ->delete();

        return response()->json(['message' => 'Movie removed from favourites successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function removeSerieFromFavourites($serieId)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthenticated User.'], 400);
            }
            Favourites::where('UserId', $user->id)
                ->where('showId', $serieId)
                ->where('type', 'tv')
                ->delete();

            return response()->json(['message' => 'Serie removed from favourites successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getFavourites(Request $request) 
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthenticated User.'], 400);
            }

            $perPage = 10;
            $page = $request->get('page', 1);

            $favourites = Favourites::where('UserId', $user->id)
                ->paginate($perPage, ['*'], 'page', $page);

            $detailedFavorites = [];
            foreach ($favourites as $favorite) {
                $url = config('services.movie_serie_api.base_api_url') .  "/$favorite->type/$favorite->showId";
                $detailedFavorite = $this->showsService->showDetails($url);
                
                if ($detailedFavorite instanceof \Illuminate\Http\JsonResponse) {
                    $detailedFavorite = $detailedFavorite->getData(true);
                }
                $cleanedFavourite = $this->showsService->filterMovieOrSerieDetails($detailedFavorite, $favorite->type);

                $detailedFavorites[] = $cleanedFavourite;
            }

            return response()->json([
                'page' => $favourites->currentPage(),
                'total_pages' => $favourites->lastPage(),
                'favourites' => $detailedFavorites,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
