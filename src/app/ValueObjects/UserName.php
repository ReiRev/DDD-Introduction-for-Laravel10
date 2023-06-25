<?php

declare(strict_types=1);

namespace App\ValueObjects;

use MichaelRubel\ValueObjects\ValueObject;
use Illuminate\Support\Facades\Validator;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @method static static make(mixed ...$values)
 * @method static static from(mixed ...$values)
 *
 * @extends ValueObject<TKey, TValue>
 */
class UserName extends ValueObject
{
    private string $username;

    protected $rules = [
        'username' => 'required|min:3|max:20'
    ];

    /**
     * Create a new instance of the value object.
     *
     * @return void
     */
    public function __construct(string $username)
    {
        $this->username = $username;
        $this->validate();
    }

    /**
     * Get the object value.
     *
     * @return string
     */
    public function value(): string
    {
        return $this->username;
    }

    /**
     * Get array representation of the value object.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            "username" => $this->username
        ];
    }

    /**
     * Get string representation of the value object.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->username;
    }

    /**
     * Validate the value object data.
     *
     * @return void
     */
    protected function validate(): void
    {
        $validator = Validator::make(
            $this->toArray(),
            $this->rules
        );
        $validator->validate();
    }
}
