<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\UserName as UserNameCast;
use App\Casts\UserId as UserIdCast;

use App\Casts\UserName as UserNameCast;

class User extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'username'
    ];

    protected $casts = [
        'username' => UserNameCast::class
    ];
}
