<?php

namespace App\Services;
use App\Models\User;
use Illuminate\Http\Request;

class UserService
{
    public function generateToken(User $user)
    {
        $existingToken = $user->tokens()
                ->where('expires_at', '>', now())
                ->orWhereNull('expires_at')
                ->first();

        if ($existingToken) {
            $existingToken->delete();
            $token = $user->createToken('authToken', ['*'], now()->addWeeks(1))->plainTextToken;
        } else {
            $token = $user->createToken('authToken', ['*'], now()->addWeeks(1))->plainTextToken;
        }

        return $token;
    }

    public function addFavourite(User $user, array $currentFavourites, array $requestItem, $type)
    {
        $showType = str_replace(['favourite', 's'], '', $type);
        if (!isset($requestItem['id'])) {
            return ['error' => 'The ' . strtolower($showType) . ' must have an "id" key.'];
        }
        
        $currentFavourites[$requestItem['id']] = $requestItem;
        $user->update([$type => json_encode($currentFavourites)]);

        return ['message' => "$showType added to favourites successfully."];
    }

    public function removeFavourite(User $user, array $currentFavourites, $itemId, $type)
    {
        $showType = str_replace(['favourite', 's'], '', $type);
        if (isset($currentFavourites[$itemId])) {
            unset($currentFavourites[$itemId]);
            $user->update([$type => json_encode($currentFavourites)]);


            return ['message' => "$showType removed from favorites successfully."];
        }

        return ['error' => "$showType not found in favorites."];
    }
}
