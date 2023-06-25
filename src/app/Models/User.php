<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\UserName as UserNameCast;
use App\Casts\UserId as UserIdCast;

class User extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'id'];

    protected $casts = [
        'id' => UserIdCast::class,
        'name' => UserNameCast::class
    ];

    // public function equals(User $user): bool
    // {
    //     return this->id == $user->id;
    // }
}
