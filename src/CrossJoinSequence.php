<?php

namespace DirectoryTree\Dummy;

use Illuminate\Support\Arr;

class CrossJoinSequence extends Sequence
{
    /**
     * Constructor.
     */
    public function __construct(mixed ...$sequences)
    {
        $crossJoined = array_map(
            function ($a) {
                return array_merge(...$a);
            },
            Arr::crossJoin(...$sequences),
        );

        parent::__construct(...$crossJoined);
    }
}
