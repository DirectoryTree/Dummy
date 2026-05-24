<?php

namespace DirectoryTree\Dummy\Tests\Fixtures;

use DirectoryTree\Dummy\DummyData;
use DirectoryTree\Dummy\HasDummyFactory;
use Faker\Generator;

class HasDummyFactoryDataStub
{
    use HasDummyFactory;

    protected static function toDummyInstance(DummyData $attributes): DummyData
    {
        return $attributes;
    }

    protected static function getDummyDefinition(Generator $faker): array
    {
        return [
            'name' => $faker->name(),
            'email' => $faker->email(),
        ];
    }
}
