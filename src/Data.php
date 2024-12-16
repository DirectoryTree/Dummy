<?php

namespace DirectoryTree\Dummy;

use ArrayAccess;
use Illuminate\Support\Arr;
use JsonSerializable;

class Data implements ArrayAccess, JsonSerializable
{
    /**
     * The data attributes.
     */
    protected array $attributes = [];

    /**
     * Constructor.
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
    public function set(string $key, mixed $value): static
    {
        data_set($this->attributes, $key, $value);

        return $this;
    }

    /**
     * Get an attribute from the data instance using "dot" notation.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->attributes, $key, $default);
    }

    /**
     * Get an attribute from the data instance.
     */
    public function value(string $key, mixed $default = null): mixed
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
     * @param  array|mixed|null  $keys
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
     * Convert the object into something JSON serializable.
     */
    public function jsonSerialize(): array
    {
        return $this->attributes;
    }

    /**
     * Determine if the given offset exists.
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * Get the value for a given offset.
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->value($offset);
    }

    /**
     * Set the value at the given offset.
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * Unset the value at the given offset.
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->attributes[$offset]);
    }

    /**
     * Handle dynamic calls to the data instance to set attributes.
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
