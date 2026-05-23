<?php

namespace DirectoryTree\Dummy\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Database\Eloquent\Model;

class EloquentFactoryStub extends EloquentFactory
{
    public function __construct(
        protected mixed $key
    ) {}

    public function definition(): array
    {
        return [];
    }

    public function create($attributes = [], ?Model $parent = null)
    {
        return new EloquentModelStub($this->key);
    }
}
