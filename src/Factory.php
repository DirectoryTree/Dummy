<?php

namespace DirectoryTree\Fakeable;

use Illuminate\Database\Eloquent\Factories\Factory as BaseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

abstract class Factory extends BaseFactory
{
    /**
     * The instance to create with the factory.
     *
     * @var string
     */
    protected $model = Fluent::class;

    /**
     * Make a new model.
     */
    public function make(mixed $attributes = [], ?Model $parent = null): mixed
    {
        $instances = parent::make($attributes, $parent);

        if ($instances instanceof Collection) {
            return $instances->map(function ($instance) {
                return $this->resolveFactoryInstance($instance);
            });
        }

        return $this->resolveFactoryInstance($instances);
    }

    /**
     * Get a new model instance.
     */
    public function newModel(array $attributes = []): FactoryCollectionProxy
    {
        // Here we will pass the model into a proxy, which contains a "newCollection"
        // method, which is required due to its use in the the base Eloquent factory
        // for creating multiple instances of a model using the "count" method.
        return new FactoryCollectionProxy(
            $this->makeModel($this->modelName(), $attributes)
        );
    }

    /**
     * Make a new model instance.
     */
    protected function makeModel(string $model, array $attributes = []): mixed
    {
        return new $model($attributes);
    }

    /**
     * Resolve the factory instance.
     */
    protected function resolveFactoryInstance(mixed $instance): mixed
    {
        return $instance instanceof FactoryCollectionProxy
            ? $instance->getInstance()
            : $instance;
    }
}
