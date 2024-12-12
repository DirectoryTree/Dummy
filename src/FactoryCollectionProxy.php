<?php

namespace DirectoryTree\Fakeable;

use Illuminate\Support\Collection;

class FactoryCollectionProxy
{
    /**
     * Constructor.
     */
    public function __construct(
        protected mixed $instance = null
    ) {}

    /**
     * Get the underlying factoried instance.
     */
    public function getInstance(): mixed
    {
        return $this->instance;
    }

    /**
     * Make a new collection.
     */
    public function newCollection(mixed $items = null): Collection
    {
        return Collection::make($items);
    }
}
