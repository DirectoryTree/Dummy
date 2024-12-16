<?php

namespace DirectoryTree\Dummy\Tests\Fixtures;

use DirectoryTree\Dummy\Data;
use DirectoryTree\Dummy\HasFactory;
use Faker\Generator;

class HasFactoryStub
{
    use HasFactory;

    protected static function toFactoryInstance(array $attributes): Data
    {
        return new Data($attributes);
    }

    protected static function getFactoryDefinition(Generator $faker): array
    {
        return [
            'name' => $faker->name(),
            'email' => $faker->email(),
        ];
    }
}
