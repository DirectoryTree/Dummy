<?php

namespace DirectoryTree\Dummy;

use Faker\Generator;

/**
 * @template TFactoryInstance of object
 */
trait HasFactory
{
    /**
     * Create a new dummy factory.
     *
     * @param  array<string, mixed>  $attributes
     * @return Factory<TFactoryInstance>
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
     *
     * @param  array<string, mixed>  $attributes
     * @return Factory<TFactoryInstance>
     */
    protected static function newFactory(array $attributes): Factory
    {
        return (new Factory(class: static::class))->state($attributes);
    }

    /**
     * Transform the dummy data into a class instance.
     *
     * @param  array<string, mixed>  $attributes
     * @return TFactoryInstance
     */
    abstract protected static function toFactoryInstance(array $attributes): mixed;

    /**
     * Define the dummy data definition.
     *
     * @return array<string, mixed>
     */
    abstract protected static function getFactoryDefinition(Generator $faker): array;
}
