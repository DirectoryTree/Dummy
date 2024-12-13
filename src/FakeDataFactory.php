<?php

namespace DirectoryTree\Fakeable;

use Closure;
use Illuminate\Support\Fluent;

class FakeDataFactory extends Factory
{
    /**
     * Build a new fake data factory with the given definition.
     */
    public static function build(Closure $definition): Fluent
    {
        return ($instance = static::new())->make(
            $definition($instance->faker)
        );
    }

    /**
     * Define the class' default state.
     */
    public function definition(): array
    {
        return [];
    }
}
