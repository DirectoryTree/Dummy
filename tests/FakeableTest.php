<?php

use DirectoryTree\Fakeable\Tests\Fixtures\FakeableInstanceStub;
use DirectoryTree\Fakeable\Tests\Fixtures\FakeableStub;
use Illuminate\Support\Fluent;

it('can generate fake fluent instance', function () {
    $instance = FakeableStub::factory()->make();

    expect($instance)->toBeInstanceOf(Fluent::class);
    expect($instance->toArray())->toHaveKeys(['name', 'email']);
});

it('can overwrite fake fluent instance data', function () {
    $instance = FakeableStub::factory()->make([
        'name' => 'John Doe',
    ]);

    expect($instance->name)->toBe('John Doe');
});

it('can generate fake instance of self', function () {
    $instance = FakeableInstanceStub::factory()->make();

    expect($instance)->toBeInstanceOf(FakeableInstanceStub::class);
    expect($instance->attributes)->toHaveKeys(['name', 'email']);
});
