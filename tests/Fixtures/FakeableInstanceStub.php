<?php

namespace DirectoryTree\Fakeable\Tests\Fixtures;

use DirectoryTree\Fakeable\Fakeable;
use Faker\Generator;
use Illuminate\Support\Fluent;

class FakeableInstanceStub
{
    use Fakeable;

    public function __construct(
        public readonly array $attributes
    ) {}

    protected static function toFakeInstance(Fluent $fluent): mixed
    {
        return new static($fluent->all());
    }

    protected static function getFakeDefinition(Generator $faker, array $attributes = []): array
    {
        return [
            'name' => $faker->name(),
            'email' => $faker->email(),
        ];
    }
}
