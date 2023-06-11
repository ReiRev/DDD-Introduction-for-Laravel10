<?php

declare(strict_types=1);

namespace App\ValueObjects;

use Illuminate\Validation\ValidationException;
use MichaelRubel\ValueObjects\ValueObject;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @method static static make(mixed ...$values)
 * @method static static from(mixed ...$values)
 *
 * @extends ValueObject<TKey, TValue>
 */
class FullName extends ValueObject
{
    private string $firstName;
    private string $lastName;

    /**
     * Create a new instance of the value object.
     *
     * @return void
     */
    public function __construct(string $firstName, string $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->validate();
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    /**
     * Get the object value.
     *
     * @return string
     */
    public function value(): string
    {
        // TODO: Implement value() method.
        return $this->firstName .  ' ' . $this->lastName;
    }

    /**
     * Get array representation of the value object.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'firstName' => $this->firstName(),
            'lastName'  => $this->lastName(),
        ];
    }

    /**
     * Check if objects are instances of same class
     * and share the same properties and values.
     *
     * @param  ValueObject<int|string, mixed>  $object
     *
     * @return bool
     */
    public function equals(ValueObject $object): bool
    {
        return  $object instanceof FullName && $this == $object;
    }

    /**
     * Inversion for `equals` method.
     *
     * @param  ValueObject<int|string, mixed>  $object
     *
     * @return bool
     */
    public function notEquals(ValueObject $object): bool
    {
        return !$this->equals($object);
    }

    /**
     * Validate the value object data.
     *
     * @return void
     */
    protected function validate(): void
    {
        // TODO: Implement validate() method.

        if (empty($this->value())) {
            throw ValidationException::withMessages([__('Value of FullName cannot be empty.')]);
        }
    }
}
