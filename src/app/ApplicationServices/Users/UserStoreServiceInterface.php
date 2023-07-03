<?php

namespace App\ApplicationServiecs\Users;

use App\DataTransferObjects\UserData;

interface UserStoreServiceInterface
{
    public function store(Userdata $userdata): void;
}
