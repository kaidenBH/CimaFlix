<?php

namespace App\Services;
use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;

class ShowsService
{
    public function fetchShow(int $page, int $batch, string $url, bool $topRated)
    {
        $apiKey = config('services.movie_serie_api.key');
        $client = new Client();
        $batch = max(1, min(2, $batch));
        $params = $topRated ? 'sort_by=vote_average.desc&without_genres=99,10755&vote_count.gte=200' : 'sort_by=popularity.desc';
        try {
            $response = $client->request('GET', "$url&language=en-US&$params&page=$page", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'accept' => 'application/json',
                ],
                'verify' => false,
            ]);

            $data = json_decode($response->getBody(), true);

            $startIndex = ($batch - 1) * 10;
            $endIndex = $batch * 10;
            $currentPage = $data['page'];
            $totalPages = $data['total_pages'];
            $slicedResults = array_slice($data['results'], $startIndex, $endIndex);
            $slicedData = [
                'page' => $currentPage,
                'batch' => $batch,
                'total_pages' => $totalPages,
                'results' => $slicedResults,
            ];
            return $slicedData;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function searchShow(int $page, int $batch, string $url)
    {
        $apiKey = config('services.movie_serie_api.key');
        $client = new Client();
        $batch = max(1, min(2, $batch));
        try {
            $response = $client->request('GET', "$url&include_adult=false&language=en-US&page=$page", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'accept' => 'application/json',
                ],
                'verify' => false,
            ]);

            $data = json_decode($response->getBody(), true);

            $startIndex = ($batch - 1) * 10;
            $endIndex = $batch * 10;
            $currentPage = $data['page'];
            $totalPages = $data['total_pages'];
            $slicedResults = array_slice($data['results'], $startIndex, $endIndex);
            $slicedData = [
                'page' => $currentPage,
                'batch' => $batch,
                'total_pages' => $totalPages,
                'results' => $slicedResults,
            ];
            return $slicedData;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showDetails(string $url)
    {
        $apiKey = config('services.movie_serie_api.key');
        $client = new Client();
        try {
            $response = $client->request('GET', "$url?language=en-US", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'accept' => 'application/json',
                ],
                'verify' => false,
            ]);

            $data = json_decode($response->getBody(), true);

            return $data;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function getTrailers(string $url)
    {
        $apiKey = config('services.movie_serie_api.key');
        $client = new Client();
        try {
            $response = $client->request('GET', "$url/videos?language=en-US", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'accept' => 'application/json',
                ],
                'verify' => false,
            ]);

            $data = json_decode($response->getBody(), true);

            $trailers = array_filter($data['results'], function ($video) {
                return $video['type'] === 'Trailer';
            });
            
            $trailers = array_reverse($trailers);

            $trailerLinks = [];
            $trailerIndex = 1;

            foreach ($trailers as $trailer) {
                $key = "Trailer " . $trailerIndex++;
                $trailerLinks[$key] = 'https://www.youtube.com/watch?v=' . $trailer['key'];
            }

            return $trailerLinks;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function filterMovieOrSerieDetails($show, $type)
    {
        $fields = $type === 'movie'
            ? ['id', 'original_title', 'overview', 'popularity', 'poster_path', 'release_date', 'title', 'vote_average', 'vote_count']
            : ['id', 'name', 'origin_country', 'original_name', 'overview', 'popularity', 'poster_path', 'vote_average', 'vote_count'];

        $filteredDetails = Arr::only($show, $fields);
        return [
            $type => $filteredDetails,
        ];
    }
}
