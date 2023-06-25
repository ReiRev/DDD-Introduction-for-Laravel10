<?php

namespace App\Casts;

use App\ValueObjects\UserName as UserNameValueObject;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class UserName implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return UserNameValueObject($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (!$value instanceof UserNameValueObject) {
            throw new InvalidArgumentException(
                'The given value is not a UserName Object.'
            );
        }

        return [
            'username' => $value->toString()
        ];
    }
}
