<?php

namespace DirectoryTree\Dummy\Tests\Fixtures;

use DirectoryTree\Dummy\Data;
use DirectoryTree\Dummy\HasFactory;
use Faker\Generator;

class HasFactoryWithEloquentAttributesStub
{
    use HasFactory;

    protected static function toFactoryInstance(Data $attributes): Data
    {
        return $attributes;
    }

    protected static function getFactoryDefinition(Generator $faker): array
    {
        return [
            'model_id' => new EloquentModelStub(123),
            'factory_id' => new EloquentFactoryStub(456),
            'dependent_id' => fn (array $attributes) => new EloquentModelStub($attributes['factory_id'] + 1),
        ];
    }
}
