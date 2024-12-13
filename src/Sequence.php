<?php

namespace DirectoryTree\Fakeable;

use Countable;

class Sequence implements Countable
{
    /**
     * The count of the sequence items.
     */
    public int $count;

    /**
     * The current index of the sequence iteration.
     */
    public int $index = 0;

    /**
     * The sequence of return values.
     */
    protected array $sequence;

    /**
     * Constructor.
     */
    public function __construct(mixed ...$sequence)
    {
        $this->sequence = $sequence;
        $this->count = count($sequence);
    }

    /**
     * Get the current count of the sequence items.
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * Get the next value in the sequence.
     */
    public function __invoke(): mixed
    {
        return tap(value($this->sequence[$this->index % $this->count], $this), function () {
            $this->index = $this->index + 1;
        });
    }
}
