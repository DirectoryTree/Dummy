<?php

namespace DirectoryTree\Dummy\Tests\Fixtures;

use DirectoryTree\Dummy\DummyData;
use DirectoryTree\Dummy\HasDummyFactory;
use Faker\Generator;

class HasDummyFactoryInstanceStub
{
    use HasDummyFactory;

    public function __construct(
        public readonly array $attributes
    ) {}

    protected static function toDummyInstance(DummyData $attributes): self
    {
        return new static($attributes->all());
    }

    protected static function getDummyDefinition(Generator $faker): array
    {
        return [
            'name' => $faker->name(),
            'email' => $faker->email(),
        ];
    }
}
