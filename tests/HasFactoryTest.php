<?php

use DirectoryTree\Dummy\Data;
use DirectoryTree\Dummy\Tests\Fixtures\HasFactoryInstanceStub;
use DirectoryTree\Dummy\Tests\Fixtures\HasFactoryStub;
use DirectoryTree\Dummy\Tests\Fixtures\HasFactoryWithStatesStub;

it('can generate fake data instance', function () {
    $instance = HasFactoryStub::factory()->make();

    expect($instance)->toBeInstanceOf(Data::class);
    expect($instance->all())->toHaveKeys(['name', 'email']);
});

it('can overwrite fake data instance data', function () {
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

it('can use dynamic state methods', function () {
    $instance = HasFactoryWithStatesStub::factory()->admin()->make();

    expect($instance)->toBeInstanceOf(Data::class);
    expect($instance->role)->toBe('admin');
    expect($instance->name)->toBe('Admin User');
    expect($instance->email)->toBe('admin@example.com');
});

it('can chain multiple dynamic state methods', function () {
    $instance = HasFactoryWithStatesStub::factory()->admin()->inactive()->make();

    expect($instance)->toBeInstanceOf(Data::class);
    expect($instance->role)->toBe('admin');
    expect($instance->name)->toBe('Admin User');
    expect($instance->email)->toBe('admin@example.com');
    expect($instance->status)->toBe('inactive');
});

it('can use dynamic state methods with premium state', function () {
    $instance = HasFactoryWithStatesStub::factory()->premium()->make();

    expect($instance)->toBeInstanceOf(Data::class);
    expect($instance->role)->toBe('premium');
    expect($instance->subscription)->toBe('premium');
});

it('throws exception for non-existent state methods', function () {
    expect(function () {
        HasFactoryWithStatesStub::factory()->nonExistentState()->make();
    })->toThrow(\BadMethodCallException::class);
});
