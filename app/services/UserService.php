<?php

namespace App\Services;
use App\Models\User;

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
}
