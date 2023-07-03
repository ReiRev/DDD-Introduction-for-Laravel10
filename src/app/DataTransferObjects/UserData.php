<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        public string $username
    ) {
    }
}
