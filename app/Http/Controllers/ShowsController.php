<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use App\Services\ShowsService;

class ShowsController extends Controller
{
    protected $showsService;

    public function __construct(ShowsService $showsService)
    {
        $this->showsService = $showsService;
    }

    public function getMovies(Request $request)
    {
        $page = $request->input('page', 1);
        $batch = $request->input('batch', 1);
        $baseMovieUrl = config('services.movie_serie_api.base_api_url') . '/discover/movie?include_adult=false&include_video=true';
        try {
            $movieList = $this->showsService->fetchShow($page, $batch, $baseMovieUrl, false);
            $data = $this->showsService->fetchShow(1, 1, $baseMovieUrl, true);
            $topMovies = array_slice($data['results'], 0, 5);

            return response()->json(['Top 5 movies' => $topMovies, 'Movie list' => $movieList]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getSeries(Request $request)
    {
        $page = $request->input('page', 1);
        $batch = $request->input('batch', 1);
        $baseSerieUrl = config('services.movie_serie_api.base_api_url') . '/discover/tv?include_adult=false&include_null_first_air_dates=false';
        try {
            $serieList = $this->showsService->fetchShow($page, $batch, $baseSerieUrl, false);
            $data = $this->showsService->fetchShow(1, 1, $baseSerieUrl, true);
            $topSeries = array_slice($data['results'], 0, 5);

            return response()->json(['Top 5 series' => $topSeries, 'Serie list' => $serieList]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function searchMovies(Request $request)
    {
        $page = $request->input('page', 1);
        $batch = $request->input('batch', 1);
        $query = $request->search;
        $baseMovieUrl = config('services.movie_serie_api.base_api_url') . "/search/movie?query=$query";
        try {
            $movieList = $this->showsService->searchShow($page, $batch, $baseMovieUrl);
            
            return response()->json(['Movie list' => $movieList]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function searchSeries(Request $request)
    {
        $page = $request->input('page', 1);
        $batch = $request->input('batch', 1);
        $query = $request->search;
        $baseSerieUrl = config('services.movie_serie_api.base_api_url') . "/search/tv?query=$query";
        try {
            $serieList = $this->showsService->searchShow($page, $batch, $baseSerieUrl);
            
            return response()->json(['Serie list' => $serieList]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function movieDetails(Request $request, $id) 
    {
        try {
            $movieId = $id;
            $movieUrl = config('services.movie_serie_api.base_api_url') . "/movie/$movieId";
            $detailedMovie = $this->showsService->showDetails($movieUrl);

            return response()->json($detailedMovie);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function serieDetails(Request $request, $id) 
    {
        try {
            $serieId = $id;
            $serieUrl = config('services.movie_serie_api.base_api_url') . "/tv/$serieId";
            $detailedSerie = $this->showsService->showDetails($serieUrl);

            return response()->json($detailedSerie);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function movieTrailers(Request $request, $id) 
    {
        try {
            $movieId = $id;
            $movieUrl = config('services.movie_serie_api.base_api_url') . "/movie/$movieId";
            $movieTrailers = $this->showsService->getTrailers($movieUrl);

            return response()->json($movieTrailers);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function serieTrailers(Request $request, $id) 
    {
        try {
            $serieId = $id;
            $serieUrl = config('services.movie_serie_api.base_api_url') . "/tv/$serieId";
            $serieTrailers = $this->showsService->getTrailers($serieUrl);

            return response()->json($serieTrailers);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
