<?php

namespace DirectoryTree\Dummy;

use ArrayAccess;
use ArrayIterator;
use BackedEnum;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use IteratorAggregate;
use JsonSerializable;
use stdClass;
use Traversable;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @implements ArrayAccess<TKey, TValue>
 * @implements IteratorAggregate<TKey, TValue>
 */
class DummyData implements ArrayAccess, IteratorAggregate, JsonSerializable
{
    /**
     * The data attributes.
     *
     * @var array<TKey, TValue>
     */
    protected array $attributes = [];

    /**
     * Constructor.
     *
     * @param  iterable<TKey, TValue>  $attributes
     */
    public function __construct(iterable $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    /**
     * Set an attribute on the data instance using "dot" notation.
     */
    public function set(string|int $key, mixed $value): static
    {
        Arr::set($this->attributes, $key, $value);

        return $this;
    }

    /**
     * Get an attribute from the data instance using "dot" notation.
     */
    public function get(string|int $key, mixed $default = null): mixed
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * Get an attribute from the data instance.
     */
    public function value(string|int $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        return value($default);
    }

    /**
     * Get the value of the given key as a new data instance.
     */
    public function scope(string $key, mixed $default = null): static
    {
        return new static(
            (array) $this->get($key, $default)
        );
    }

    /**
     * Get the attributes from the data instance.
     *
     * @param  array<int, string>|string|int|null  $keys
     * @return array<TKey, TValue>
     */
    public function all(mixed $keys = null): array
    {
        $data = $this->attributes;

        if (! $keys) {
            return $data;
        }

        $results = [];

        foreach (is_array($keys) ? $keys : func_get_args() as $key) {
            Arr::set($results, $key, Arr::get($data, $key));
        }

        return $results;
    }

    /**
     * Determine if the data contains a given key.
     */
    public function exists(string|int|array $key, string|int ...$keys): bool
    {
        return $this->has($key, ...$keys);
    }

    /**
     * Determine if the data contains a given key.
     */
    public function has(string|int|array $key, string|int ...$keys): bool
    {
        $keys = is_array($key) ? $key : [$key, ...$keys];

        foreach ($keys as $value) {
            if (! Arr::has($this->attributes, (string) $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the data contains any of the given keys.
     */
    public function hasAny(string|int|array $key, string|int ...$keys): bool
    {
        return Arr::hasAny(
            $this->attributes,
            array_map('strval', is_array($key) ? $key : [$key, ...$keys])
        );
    }

    /**
     * Determine if the data is missing a given key.
     */
    public function missing(string|int|array $key, string|int ...$keys): bool
    {
        return ! $this->has($key, ...$keys);
    }

    /**
     * Determine if the data contains a non-empty value for the given key.
     */
    public function filled(string|int|array $key, string|int ...$keys): bool
    {
        $keys = is_array($key) ? $key : [$key, ...$keys];

        foreach ($keys as $value) {
            if ($this->isEmptyString($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the data contains an empty value for the given key.
     */
    public function isNotFilled(string|int|array $key, string|int ...$keys): bool
    {
        return ! $this->filled($key, ...$keys);
    }

    /**
     * Determine if the data contains an empty value for the given key.
     */
    public function notFilled(string|int|array $key, string|int ...$keys): bool
    {
        return $this->isNotFilled($key, ...$keys);
    }

    /**
     * Determine if the data contains a non-empty value for any of the given keys.
     */
    public function anyFilled(string|int|array $key, string|int ...$keys): bool
    {
        $keys = is_array($key) ? $key : [$key, ...$keys];

        foreach ($keys as $value) {
            if ($this->filled($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve data from the instance as a boolean.
     */
    public function boolean(string|int $key, bool $default = false): bool
    {
        return filter_var($this->get($key, $default), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Retrieve data from the instance as an integer.
     */
    public function integer(string|int $key, int $default = 0): int
    {
        return (int) $this->get($key, $default);
    }

    /**
     * Retrieve data from the instance as a float.
     */
    public function float(string|int $key, float $default = 0.0): float
    {
        return (float) $this->get($key, $default);
    }

    /**
     * Retrieve data from the instance as an array.
     */
    public function array(array|string|int $key): array
    {
        return (array) (is_array($key) ? $this->only($key) : $this->get($key));
    }

    /**
     * Retrieve data from the instance as a collection.
     */
    public function collect(array|string|int $key): Collection
    {
        return new Collection(is_array($key) ? $this->only($key) : $this->get($key));
    }

    /**
     * Retrieve data from the instance as an enum.
     *
     * @template TEnum of BackedEnum
     * @template TDefault of TEnum|null
     *
     * @param  class-string<TEnum>  $enumClass
     * @param  TDefault  $default
     * @return TEnum|TDefault
     */
    public function enum(string|int $key, string $enumClass, mixed $default = null): mixed
    {
        if ($this->isNotFilled($key) || ! $this->isBackedEnum($enumClass)) {
            return value($default);
        }

        return $enumClass::tryFrom($this->get($key)) ?: value($default);
    }

    /**
     * Retrieve data from the instance as an array of enums.
     *
     * @template TEnum of BackedEnum
     *
     * @param  class-string<TEnum>  $enumClass
     * @return array<int, TEnum>
     */
    public function enums(string|int $key, string $enumClass): array
    {
        if ($this->isNotFilled($key) || ! $this->isBackedEnum($enumClass)) {
            return [];
        }

        return $this->collect($key)
            ->map(fn (mixed $value) => $enumClass::tryFrom($value))
            ->filter()
            ->all();
    }

    /**
     * Get a subset containing the provided keys with values from the data.
     */
    public function only(mixed $keys): array
    {
        $results = [];

        $placeholder = new stdClass;

        foreach (is_array($keys) ? $keys : func_get_args() as $key) {
            $value = Arr::get($this->attributes, $key, $placeholder);

            if ($value !== $placeholder) {
                Arr::set($results, $key, $value);
            }
        }

        return $results;
    }

    /**
     * Get all of the data except for a specified array of items.
     */
    public function except(mixed $keys): array
    {
        $results = $this->attributes;

        Arr::forget($results, is_array($keys) ? $keys : func_get_args());

        return $results;
    }

    /**
     * Determine if the given key is an empty string for "filled".
     */
    protected function isEmptyString(string|int $key): bool
    {
        $value = $this->get($key);

        return ! is_bool($value)
            && ! is_array($value)
            && trim((string) $value) === '';
    }

    /**
     * Determine if the given enum class is backed.
     *
     * @param  class-string  $enumClass
     */
    protected function isBackedEnum(string $enumClass): bool
    {
        return is_a($enumClass, BackedEnum::class, true);
    }

    /**
     * Get the attributes from the data instance.
     *
     * @return array<TKey, TValue>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Convert the data instance to an array.
     *
     * @return array<TKey, TValue>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * Convert the data instance to JSON.
     */
    public function toJson($options = 0): string
    {
        return (string) json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array<TKey, TValue>
     */
    public function jsonSerialize(): array
    {
        return $this->attributes;
    }

    /**
     * Get an iterator for the attributes.
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->attributes);
    }

    /**
     * Determine if the given offset exists.
     *
     * @param  string|int  $offset
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  string|int  $offset
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->value($offset);
    }

    /**
     * Set the value at the given offset.
     *
     * @param  string|int|null  $offset
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * Unset the value at the given offset.
     *
     * @param  string|int  $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->attributes[$offset]);
    }

    /**
     * Handle dynamic calls to the data instance to set attributes.
     *
     * @param  array<int, mixed>  $parameters
     */
    public function __call(string $method, array $parameters): static
    {
        $this->attributes[$method] = count($parameters) > 0 ? reset($parameters) : true;

        return $this;
    }

    /**
     * Dynamically retrieve the value of an attribute.
     */
    public function __get(string $key): mixed
    {
        return $this->value($key);
    }

    /**
     * Dynamically set the value of an attribute.
     */
    public function __set(string $key, mixed $value): void
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Dynamically check if an attribute is set.
     */
    public function __isset(string $key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * Dynamically unset an attribute.
     */
    public function __unset(string $key): void
    {
        $this->offsetUnset($key);
    }
}
