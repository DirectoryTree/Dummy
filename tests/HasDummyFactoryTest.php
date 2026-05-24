<?php

use DirectoryTree\Dummy\DummyData;
use DirectoryTree\Dummy\Tests\Fixtures\HasDummyFactoryDataStub;
use DirectoryTree\Dummy\Tests\Fixtures\HasDummyFactoryInstanceStub;
use DirectoryTree\Dummy\Tests\Fixtures\HasDummyFactoryWithReservedFactoryStub;
use DirectoryTree\Dummy\Tests\Fixtures\HasDummyFactoryWithStatesStub;

it('can generate fake data instance', function () {
    $instance = HasDummyFactoryDataStub::dummy()->make();

    expect($instance)->toBeInstanceOf(DummyData::class);
    expect($instance->all())->toHaveKeys(['name', 'email']);
});

it('can overwrite fake data instance data', function () {
    $instance = HasDummyFactoryDataStub::dummy()->make([
        'name' => 'John Doe',
    ]);

    expect($instance->name)->toBe('John Doe');
});

it('can generate fake data when factory is reserved by the target class', function () {
    $instance = HasDummyFactoryWithReservedFactoryStub::dummy()->make([
        'name' => 'John Doe',
    ]);

    expect(HasDummyFactoryWithReservedFactoryStub::factory())->toBe('reserved');
    expect($instance)->toBeInstanceOf(DummyData::class);
    expect($instance->name)->toBe('John Doe');
});

it('passes data into the factory instance transformer', function () {
    $instance = HasDummyFactoryInstanceStub::dummy([
        'active' => 'true',
        'visits' => '5',
    ])->make();

    expect($instance->attributes)->toHaveKeys(['name', 'email', 'active', 'visits']);
    expect($instance->attributes['active'])->toBe('true');
    expect($instance->attributes['visits'])->toBe('5');
});

it('can generate fake instance of self', function () {
    $instance = HasDummyFactoryInstanceStub::dummy()->make();

    expect($instance)->toBeInstanceOf(HasDummyFactoryInstanceStub::class);
    expect($instance->attributes)->toHaveKeys(['name', 'email']);
});

it('can use dynamic state methods', function () {
    $instance = HasDummyFactoryWithStatesStub::dummy()->admin()->make();

    expect($instance)->toBeInstanceOf(DummyData::class);
    expect($instance->role)->toBe('admin');
    expect($instance->name)->toBe('Admin User');
    expect($instance->email)->toBe('admin@example.com');
});

it('can chain multiple dynamic state methods', function () {
    $instance = HasDummyFactoryWithStatesStub::dummy()->admin()->inactive()->make();

    expect($instance)->toBeInstanceOf(DummyData::class);
    expect($instance->role)->toBe('admin');
    expect($instance->name)->toBe('Admin User');
    expect($instance->email)->toBe('admin@example.com');
    expect($instance->status)->toBe('inactive');
});

it('can use dynamic state methods with premium state', function () {
    $instance = HasDummyFactoryWithStatesStub::dummy()->premium()->make();

    expect($instance)->toBeInstanceOf(DummyData::class);
    expect($instance->role)->toBe('premium');
    expect($instance->subscription)->toBe('premium');
});

it('throws exception for non-existent state methods', function () {
    expect(function () {
        HasDummyFactoryWithStatesStub::dummy()->nonExistentState()->make();
    })->toThrow(BadMethodCallException::class);
});
