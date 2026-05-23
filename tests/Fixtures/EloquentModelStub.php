<?php

namespace DirectoryTree\Dummy\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

class EloquentModelStub extends Model
{
    public function __construct(
        protected mixed $key = null
    ) {
        parent::__construct();
    }

    public function getKey(): mixed
    {
        return $this->key;
    }
}
