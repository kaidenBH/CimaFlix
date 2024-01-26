<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    public function createToken($name, array $abilities = ['*'])
    {
        return parent::createToken($this->tokenable->username, $abilities);
    }
}
