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
            ($factory = static::getFakeFactory())->make(
                static::getFakeDefinition($factory->faker(), $attributes)
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
     * Get the fake data factory instance.
     */
    protected static function getFakeFactory(): Factory
    {
        return FakeDataFactory::new();
    }

    /**
     * Define the fake data definition.
     */
    abstract protected static function getFakeDefinition(Generator $faker, array $attributes = []): array;
}
