<?php

use DirectoryTree\Dummy\CrossJoinSequence;
use DirectoryTree\Dummy\Sequence;
use DirectoryTree\Dummy\Tests\Fixtures\FactoryStub;

it('reports its size via count and Countable', function () {
    $sequence = new Sequence('a', 'b', 'c');

    expect($sequence->count())->toBe(3);
    expect(count($sequence))->toBe(3);
    expect($sequence->count)->toBe(3);
});

it('returns sequence values in order when invoked', function () {
    $sequence = new Sequence('a', 'b', 'c');

    expect($sequence())->toBe('a');
    expect($sequence())->toBe('b');
    expect($sequence())->toBe('c');
});

it('wraps around after exhausting the sequence', function () {
    $sequence = new Sequence('a', 'b');

    expect($sequence())->toBe('a');
    expect($sequence())->toBe('b');
    expect($sequence())->toBe('a');
    expect($sequence())->toBe('b');
});

it('increments the index after each invocation', function () {
    $sequence = new Sequence('a', 'b');

    expect($sequence->index)->toBe(0);
    $sequence();
    expect($sequence->index)->toBe(1);
    $sequence();
    expect($sequence->index)->toBe(2);
});

it('resolves closure values and passes itself as context', function () {
    $sequence = new Sequence(
        fn (Sequence $self) => "index-{$self->index}",
        fn (Sequence $self) => "index-{$self->index}",
    );

    expect($sequence())->toBe('index-0');
    expect($sequence())->toBe('index-1');
});

it('produces the cartesian product of all input sequences', function () {
    $sequence = new CrossJoinSequence(
        [['a' => 1], ['a' => 2]],
        [['b' => 3], ['b' => 4]],
    );

    expect($sequence->count())->toBe(4);

    expect($sequence())->toBe(['a' => 1, 'b' => 3]);
    expect($sequence())->toBe(['a' => 1, 'b' => 4]);
    expect($sequence())->toBe(['a' => 2, 'b' => 3]);
    expect($sequence())->toBe(['a' => 2, 'b' => 4]);
});

it('can apply a cross joined sequence through the factory', function () {
    $collection = FactoryStub::new()
        ->count(4)
        ->crossJoinSequence(
            [['role' => 'admin'], ['role' => 'user']],
            [['active' => true], ['active' => false]],
        )
        ->make();

    expect($collection[0]->role)->toBe('admin');
    expect($collection[0]->active)->toBeTrue();
    expect($collection[1]->role)->toBe('admin');
    expect($collection[1]->active)->toBeFalse();
    expect($collection[2]->role)->toBe('user');
    expect($collection[2]->active)->toBeTrue();
    expect($collection[3]->role)->toBe('user');
    expect($collection[3]->active)->toBeFalse();
});
