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
class UserId extends ValueObject
{
    private string $userId;

    protected $rules = [
        "userId" => "required"
    ];

    /**
     * Create a new instance of the value object.
     *
     * @return void
     */
    public function __construct(string $userId)
    {
        $this->userId = $userId;
        $this->validate();
    }

    /**
     * Get the object value.
     *
     * @return string
     */
    public function value(): string
    {
        return $this->userId;
    }

    /**
     * Get array representation of the value object.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            "userId" => $this->userId
        ];
    }

    /**
     * Get string representation of the value object.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->userId;
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
