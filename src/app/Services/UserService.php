<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function exists(User $user)
    {
        return User::where('name', $user->name)->first() !== null;
    }
}
