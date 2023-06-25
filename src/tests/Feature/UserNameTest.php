<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\ValueObjects\UserName;

use Exception;

class UserNameTest extends TestCase
{
    public function test_empty_input(): void
    {
        $this->expectException(Exception::class);
        new UserName('');
    }

    public function shortNames(): array
    {
        return [
            ['a'],
            ['ab']
        ];
    }

    /**
     * @dataProvider shortNames
     */
    public function test_short_name(string $name): void
    {
        $this->expectException(Exception::class);
        new UserName($name);
    }

    public function longNames(): array
    {
        return [
            ['UE69KHLM3Q8BzwM6ZDAYa'], // 21 letters
            ['UE69KHLM3Q8BzwM6ZDAYad'], // 22 letters
        ];
    }

    /**
     * @dataProvider longNames
     */
    public function test_long_name(string $name): void
    {
        $this->expectException(Exception::class);
        new UserName($name);
    }

    public function validNames(): array
    {
        return [
            ['ReiRev'],
            ['abc'], // 3 letters
            ['eFaNtc7hZMRXn8Ud7y2g'], // 20 letters
            ['れいれぶ']
        ];
    }

    /**
     * @dataProvider validNames
     */
    public function test_valid_name(string $name): void
    {
        $username = new UserName($name);
        $this->assertEquals($name, $username->toString());
        $this->assertEquals($name, $username->value());
        $this->assertEquals(['username' => $name], $username->toArray());
    }
}
