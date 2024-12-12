<?php

namespace DirectoryTree\Fakeable\Tests\Fixtures;

use DirectoryTree\Fakeable\Fakeable;
use Faker\Generator;

class FakeableStub
{
    use Fakeable;

    protected static function getFakeDefinition(Generator $faker, array $attributes = []): array
    {
        return [
            'name' => $faker->name(),
            'email' => $faker->email(),
        ];
    }
}
