<?php

namespace DirectoryTree\Dummy\Tests\Fixtures;

use DirectoryTree\Dummy\HasFactory;
use Faker\Generator;
use Illuminate\Support\Fluent;

class HasFactoryStub
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
