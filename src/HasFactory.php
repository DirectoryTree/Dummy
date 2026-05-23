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
            fn (Generator $faker, array $attributes) => static::toFactoryInstance($attributes)
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
        return (new Factory(class: static::class))->state(function () {
            return static::getFactoryDefinition($this->faker());
        })->state($attributes);
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
