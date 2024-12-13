<?php

namespace DirectoryTree\Fakeable\Tests\Fixtures;

use DirectoryTree\Fakeable\HasFactory;
use Faker\Generator;
use Illuminate\Support\Fluent;

class FakeableStub
{
    use HasFactory;

    protected static function toFactoryInstance(array $attributes): Fluent
    {
        return new Fluent($attributes);
    }

    protected static function getFactoryDefinition(Generator $faker): array
    {
        return [
            'name' => $faker->name(),
            'email' => $faker->email(),
        ];
    }
}
