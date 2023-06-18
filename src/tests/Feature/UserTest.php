<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Services\UserService;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_create(): void
    {
        $reirev = new User(['name' => 'Rei Rev']);
        $reirev->save();

        $head = User::find(1)->get()->first();

        $this->assertEquals($head['name'], 'Rei Rev');
    }
}
