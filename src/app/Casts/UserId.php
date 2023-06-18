<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class UserId implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('The value ' . $value . ' must be string.');
        }
        if (empty($value)) {
            throw new \InvalidArgumentException('The value must have value.');
        }
        return $value;
    }
}
