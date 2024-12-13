<?php

namespace DirectoryTree\Fakeable;

use Faker\Generator;

trait HasFactory
{
    /**
     * Create fake data for the instance.
     */
    public static function factory(array $attributes = []): Factory
    {
        return static::newFactory($attributes)->using(
            function (Generator $faker, array $attributes) {
                return static::toFactoryInstance(
                    array_merge(static::getFactoryDefinition($faker, $attributes), $attributes)
                );
            }
        );
    }

    /**
     * Get a new factory instance.
     */
    protected static function newFactory(array $attributes): Factory
    {
        return StaticFactory::new($attributes);
    }

    /**
     * Transform the fake data into a class instance.
     */
    abstract protected static function toFactoryInstance(array $attributes): mixed;

    /**
     * Define the fake data definition.
     */
    abstract protected static function getFactoryDefinition(Generator $faker): array;
}
