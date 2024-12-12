<?php

use DirectoryTree\Fakeable\Tests\Fixtures\FakeableInstanceStub;
use DirectoryTree\Fakeable\Tests\Fixtures\FakeableStub;
use Illuminate\Support\Fluent;

it('can generate fake fluent instance', function () {
    $instance = FakeableStub::fake();

    expect($instance)->toBeInstanceOf(Fluent::class);
    expect($instance->toArray())->toHaveKeys(['name', 'email']);
});

it('can generate fake instance of self', function () {
    $instance = FakeableInstanceStub::fake();

    expect($instance)->toBeInstanceOf(FakeableInstanceStub::class);
    expect($instance->attributes)->toHaveKeys(['name', 'email']);
});
