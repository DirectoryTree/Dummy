<?php

namespace DirectoryTree\Dummy\Tests\Fixtures;

use DirectoryTree\Dummy\HasFactory;
use Faker\Generator;

class HasFactoryInstanceStub
{
    use HasFactory;

    public function __construct(
        public readonly array $attributes
    ) {}

    protected static function toFactoryInstance(array $attributes): self
    {
        return new static($attributes);
    }

    protected static function getFactoryDefinition(Generator $faker): array
    {
        return [
            'name' => $faker->name(),
            'email' => $faker->email(),
        ];
    }
}
