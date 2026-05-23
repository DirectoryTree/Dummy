<?php

namespace DirectoryTree\Dummy;

use Illuminate\Support\Arr;

class CrossJoinSequence extends Sequence
{
    /**
     * Constructor.
     *
     * @param  array<int, array<string, mixed>>  ...$sequences
     */
    public function __construct(mixed ...$sequences)
    {
        $crossJoined = array_map(
            function (array $attributes) {
                return array_merge(...$attributes);
            },
            Arr::crossJoin(...$sequences),
        );

        parent::__construct(...$crossJoined);
    }
}
