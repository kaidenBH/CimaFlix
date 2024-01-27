# Test Backend Movies
## Overview:

This **CimaFlix** is a Backend application made with Laravel Sail along with MySql database. It was developed to use an external api for fetching and searching for movies and series. and was made to manage these movies and series in the app that was implemented.

## How to set-up and use:

- Clone this Repository to your local machine.
- Change the provided **.env** file to meet your needs such as setting up your Database requirment, and change the **MOVIE_API_KEY** to your own (you can get it from https://www.themoviedb.org/documentation/api), and lastly change the **EXTERNAL_API** if needed.
- The app is configured with sail, so you can open docker desktop or any docker provider that accepts WSL2 and run **sail up -d**, and then migrate the db by **sail artisan migrate** and youre good to go!
- After that you can use Postman (or any app pf your choice) to make api requests on the port 80 like: **http://localhost:80/api/...**.

## Database migrations design:

I went with a user table that has this structure: 
<pre>
 $table->id();
 $table->string('username')->unique();
 $table->string('password');
 $table->rememberToken();
 $table->timestamps();
</pre>
and a favourties table to save the user is alongside with either the movie id or serie id while setting the type for each one. It's structured like this:
<pre>
 $table->id();
 $table->integer('showId');
 $table->string('type');
 $table->unsignedBigInteger('UserId');
 $table->foreign('UserId')->references('id')->on('users')->onDelete('cascade');
 $table->timestamps();
</pre>


## API endpoints usage: 
There are many api endpoints, here's a brief explanation on some of them:
- ***/auth/signUp*:** The user would send a **post** request here if he wants to create a new account and wil be required to send these information along **{ username, password }** then if the request is a success it will return the user details and a unique token that lasts for a week, that will give the user authorization to access some api endpoints.

- ***/auth/signIn*:** if the user already have an account he can send a **post** request to this endpoint to send his **{ username, password }** to retrieve a unique token that lasts for a week.

- ***/show/movieList*** and ***/show/serieList*** are **get** requests for fetching movies/series that would give back the top 5 rated movies/series and then 10 items per page sorted by popularity.

- ***/show/searchMovies*** and ***/show/searchSeries*** are **get** requests for searching for a specific movie/serie by sending a **search** string in the request which waht you want to be searched in titles, original titles.

- ***/show/movie/{id}*:** and ***/show/serie/{id}*:** are **get** requests for getting the more details about a movie/serie.

- ***/show/movieTrailers/{id}*** and ***/show/serieTrailers/{id}*** are **get** requests for getting the trailers of a movie/serie.

- ***/show/addMovieToFavourites/{movieId}*** and ***/show/addSerieToFavourites/{serieId}*** are **put** requests to make a movie/serie as favourites. 

- ***/show/removeMovieToFavourites/{movieId}*** and ***/show/removeSerieToFavourites/{serieId}*** are **put** requests to remove a movie/serie from favourites. 

- ***/show/favourites*:** this is a **get** request to fetch all the favourite movies and series and paginate them to be only 10 items per page. 


[Go here for all the requests with examples.](https://documenter.getpostman.com/view/28993914/2s9Yyqi2kk)
