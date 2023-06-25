<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\ValueObjects\UserName;

use Exception;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_create(): void
    {
        $username = new UserName('ReiRev');
        $user = User::create(['username' => $username]);

        $head = User::first();

        $this->assertEquals($user['username'], 'ReiRev');
        $this->assertEquals($head['username'], 'ReiRev');
    }

    public function test_duplicate_user_create(): void
    {
        $username = new UserName('ReiRev');
        $user = User::create(['username' => $username]);
        $this->expectException(Exception::class);
        $user = User::create(['username' => $username]);
    }
}
