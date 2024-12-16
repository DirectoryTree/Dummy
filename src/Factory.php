<?php

namespace DirectoryTree\Dummy;

use Closure;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;

class Factory
{
    use Conditionable;

    /**
     * The current Faker instance.
     */
    protected Generator $faker;

    /**
     * Constructor.
     */
    public function __construct(
        protected ?int $count = null,
        protected ?Closure $using = null,
        protected Collection $states = new Collection,
        protected Collection $afterMaking = new Collection,
    ) {
        $this->faker = $this->newFaker();
    }

    /**
     * Get a new dummy factory instance for the given attributes.
     */
    public static function new(Closure|array $attributes = []): static
    {
        return (new static)->state($attributes)->configure();
    }

    /**
     * Get a new dummy factory instance for the given number of classes.
     */
    public static function times(int $count): static
    {
        return static::new()->count($count);
    }

    /**
     * Define the dummy class' default state.
     */
    protected function definition(): array
    {
        return [];
    }

    /**
     * Generate a new dummy class instance.
     */
    protected function generate(array $attributes): mixed
    {
        return new Data($attributes);
    }

    /**
     * Configure the dummy factory.
     */
    protected function configure(): static
    {
        return $this;
    }

    /**
     * Create a new collection of dummy instances.
     */
    protected function collect(array $instances = []): mixed
    {
        return new Collection($instances);
    }

    /**
     * Get the raw attributes generated by the factory.
     */
    public function raw(Closure|array $attributes = []): array
    {
        if ($this->count === null) {
            return $this->state($attributes)->getExpandedAttributes();
        }

        return array_map(function () use ($attributes) {
            return $this->state($attributes)->getExpandedAttributes();
        }, range(1, $this->count));
    }

    /**
     * Create a collection of classes.
     */
    public function make(Closure|array $attributes = []): mixed
    {
        if (! empty($attributes)) {
            return $this->state($attributes)->make();
        }

        if ($this->count === null) {
            return tap($this->makeInstance(), function ($instance) {
                $this->callAfterMaking($this->collect([$instance]));
            });
        }

        if ($this->count < 1) {
            return $this->collect();
        }

        $instances = $this->collect(array_map(function () {
            return $this->makeInstance();
        }, range(1, $this->count)));

        $this->callAfterMaking($instances);

        return $instances;
    }

    /**
     * Make an instance of the class with the given attributes.
     */
    protected function makeInstance(): mixed
    {
        $attributes = $this->getExpandedAttributes();

        if ($this->using) {
            return ($this->using)($this->faker, $attributes);
        }

        return $this->generate($attributes);
    }

    /**
     * Get a raw attributes array for the class.
     */
    protected function getExpandedAttributes(): array
    {
        return $this->expandAttributes($this->getRawAttributes());
    }

    /**
     * Get the raw attributes for the class as an array.
     */
    protected function getRawAttributes(): array
    {
        return $this->states->pipe(function ($states) {
            return $states;
        })->reduce(function ($carry, $state) {
            if ($state instanceof Closure) {
                $state = $state->bindTo($this);
            }

            return array_merge($carry, $state($carry));
        }, $this->definition());
    }

    /**
     * Expand all attributes to their underlying values.
     */
    protected function expandAttributes(array $definition): array
    {
        return (new Collection($definition))->map(function ($attribute, $key) use (&$definition) {
            if (is_callable($attribute) && ! is_string($attribute) && ! is_array($attribute)) {
                $attribute = $attribute($definition);
            }

            $definition[$key] = $attribute;

            return $attribute;
        })->all();
    }

    /**
     * Add a new state transformation to the class definition.
     */
    public function state(mixed $state): static
    {
        return $this->newInstance([
            'states' => $this->states->concat([
                is_callable($state) ? $state : function () use ($state) {
                    return $state;
                },
            ]),
        ]);
    }

    /**
     * Set a single class attribute.
     */
    public function set(string|int $key, mixed $value): static
    {
        return $this->state([$key => $value]);
    }

    /**
     * Add a new sequenced state transformation to the class definition.
     */
    public function sequence(mixed ...$sequence): static
    {
        return $this->state(new Sequence(...$sequence));
    }

    /**
     * Add a new sequenced state transformation to the class definition and update the pending creation count to the size of the sequence.
     */
    public function forEachSequence(array ...$sequence): static
    {
        return $this->state(new Sequence(...$sequence))->count(count($sequence));
    }

    /**
     * Add a new cross joined sequenced state transformation to the class definition.
     */
    public function crossJoinSequence(array ...$sequence): static
    {
        return $this->state(new CrossJoinSequence(...$sequence));
    }

    /**
     * Set the callback to be used to create the class instance.
     */
    public function using(Closure $callback): static
    {
        return $this->newInstance([
            'using' => $callback,
        ]);
    }

    /**
     * Add a new "after making" callback to the class definition.
     */
    public function afterMaking(Closure $callback): static
    {
        return $this->newInstance([
            'afterMaking' => $this->afterMaking->concat([$callback]),
        ]);
    }

    /**
     * Call the "after making" callbacks for the given class instances.
     */
    protected function callAfterMaking(iterable $instances): void
    {
        foreach ($instances as $instance) {
            $this->afterMaking->each(
                fn ($callback) => $callback($instance)
            );
        }
    }

    /**
     * Specify how many dummy classes should be generated.
     */
    public function count(?int $count): static
    {
        return $this->newInstance(['count' => $count]);
    }

    /**
     * Create a new instance of the factory builder with the given mutated properties.
     */
    protected function newInstance(array $arguments = []): static
    {
        return new static(...array_values(array_merge([
            'count' => $this->count,
            'using' => $this->using,
            'states' => $this->states,
            'afterMaking' => $this->afterMaking,
        ], $arguments)));
    }

    /**
     * Get the Faker instance.
     */
    public function faker(): Generator
    {
        return $this->faker ??= $this->newFaker();
    }

    /**
     * Get a new Faker instance.
     */
    protected function newFaker(): Generator
    {
        if (class_exists(Container::class)) {
            return Container::getInstance()->make(Generator::class);
        }

        return FakerFactory::create();
    }
}
