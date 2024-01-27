<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserSignUp;
use App\Http\Requests\UserSignIn;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\UserService;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function signup(UserSignUp $request)
    {
        try {
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
            ]);

            //$token = $user->createToken('authToken')->plainTextToken;
            $token = $user->createToken('authToken', ['*'], now()->addWeeks(1))->plainTextToken;

            return response()->json(['user' => $user, 'token' => $token, 'message' => 'User registered successfully'], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] === 1062) {
                return response()->json(['message' => 'This Username already exists.'], 400);
            }
            return response()->json(['message' => 'User registration failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function signin(UserSignIn $request)
    {
        try {
            if (!auth()->attempt(['username' => $request->username, 'password' => $request->password])) {
                return response()->json(['message' => 'Invalid credentials'], 400);
            }

            $user = auth()->user();
            $user->makeHidden(['favouriteMovies', 'favouriteSeries']);
            $token = $this->userService->generateToken($user);

            return response()->json(['user' => $user, 'token' => $token, 'message' => 'User authenticated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'User login failed', 'error' => $e->getMessage()], 500);
        }
    }
}
