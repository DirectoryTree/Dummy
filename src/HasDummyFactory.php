<?php

namespace DirectoryTree\Dummy;

use Faker\Generator;

/**
 * @template TFactoryInstance of object
 */
trait HasDummyFactory
{
    /**
     * Create a new dummy factory.
     *
     * @param  array<string, mixed>  $attributes
     * @return Factory<TFactoryInstance>
     */
    public static function dummy(array $attributes = []): Factory
    {
        return static::newDummyFactory($attributes)->using(
            fn (Generator $faker, array $attributes) => static::toDummyInstance(new DummyData($attributes))
        );
    }

    /**
     * Get a new dummy factory instance.
     *
     * @param  array<string, mixed>  $attributes
     * @return Factory<TFactoryInstance>
     */
    protected static function newDummyFactory(array $attributes): Factory
    {
        $class = static::class;

        return (new Factory(class: static::class))->state(function () use ($class) {
            return $class::getDummyDefinition($this->faker());
        })->state($attributes);
    }

    /**
     * Transform the dummy data into a class instance.
     *
     * @param  DummyData<string, mixed>  $attributes
     * @return TFactoryInstance
     */
    abstract protected static function toDummyInstance(DummyData $attributes): mixed;

    /**
     * Define the dummy data definition.
     *
     * @return array<string, mixed>
     */
    abstract protected static function getDummyDefinition(Generator $faker): array;
}
