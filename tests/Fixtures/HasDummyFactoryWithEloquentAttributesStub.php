<?php

namespace DirectoryTree\Dummy\Tests\Fixtures;

use DirectoryTree\Dummy\DummyData;
use DirectoryTree\Dummy\HasDummyFactory;
use Faker\Generator;

class HasDummyFactoryWithEloquentAttributesStub
{
    use HasDummyFactory;

    protected static function toDummyInstance(DummyData $attributes): DummyData
    {
        return $attributes;
    }

    protected static function getDummyDefinition(Generator $faker): array
    {
        return [
            'model_id' => new EloquentModelStub(123),
            'factory_id' => new EloquentFactoryStub(456),
            'dependent_id' => fn (array $attributes) => new EloquentModelStub($attributes['factory_id'] + 1),
        ];
    }
}
