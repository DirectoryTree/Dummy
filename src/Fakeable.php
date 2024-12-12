<?php

namespace DirectoryTree\Fakeable;

use Faker\Generator;
use Illuminate\Support\Fluent;

trait Fakeable
{
    /**
     * Create fake data for the instance.
     *
     * @return Fluent|static
     */
    public static function fake(array $attributes = []): mixed
    {
        return static::toFakeInstance(
            FakeDataFactory::build(
                fn (Generator $faker) => array_merge(
                    static::getFakeDefinition($faker, $attributes), $attributes
                ),
            )
        );
    }

    /**
     * Transform the fake data into a class instance.
     */
    protected static function toFakeInstance(Fluent $fluent): mixed
    {
        return $fluent;
    }

    /**
     * Define the fake data definition.
     */
    abstract protected static function getFakeDefinition(Generator $faker, array $attributes = []): array;
}
