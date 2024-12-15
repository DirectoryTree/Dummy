<?php

use DirectoryTree\Dummy\Tests\Fixtures\HasFactoryInstanceStub;
use DirectoryTree\Dummy\Tests\Fixtures\HasFactoryStub;
use Illuminate\Support\Fluent;

it('can generate fake fluent instance', function () {
    $instance = HasFactoryStub::factory()->make();

    expect($instance)->toBeInstanceOf(Fluent::class);
    expect($instance->toArray())->toHaveKeys(['name', 'email']);
});

it('can overwrite fake fluent instance data', function () {
    $instance = HasFactoryStub::factory()->make([
        'name' => 'John Doe',
    ]);

    expect($instance->name)->toBe('John Doe');
});

it('can generate fake instance of self', function () {
    $instance = HasFactoryInstanceStub::factory()->make();

    expect($instance)->toBeInstanceOf(HasFactoryInstanceStub::class);
    expect($instance->attributes)->toHaveKeys(['name', 'email']);
});
