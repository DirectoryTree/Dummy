<?php

namespace DirectoryTree\Fakeable\Tests\Fixtures;

use DirectoryTree\Fakeable\HasFactory;
use Faker\Generator;

class FakeableInstanceStub
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
