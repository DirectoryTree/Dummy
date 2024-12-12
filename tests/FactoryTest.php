<?php

use DirectoryTree\Fakeable\Tests\Fixtures\FakeFactoryStub;
use Illuminate\Support\Fluent;

it('can generate single instance', function () {
    $instance = FakeFactoryStub::new()->make();

    expect($instance)->toBeInstanceOf(Fluent::class);
});

it('can generate multiple instances in a collection', function () {
    $collection = FakeFactoryStub::new()->count(5)->make();

    $collection->ensure(Fluent::class);

    expect($collection)->toHaveCount(5);
});

it('can accept attributes', function () {
    $instance = FakeFactoryStub::new([
        'id' => 1,
        'name' => 'foo',
    ])->make();

    expect($instance->id)->toBe(1);
    expect($instance->name)->toBe('foo');
});

it('can make raw attributes', function () {
    $raw = FakeFactoryStub::new()->count(5)->raw();

    expect($raw)->toBeArray();
    expect($raw[0])->toHaveKeys(['name', 'email']);
});

it('can make many raw attributes', function () {
    $raws = FakeFactoryStub::new()->count(5)->raw();

    expect($raws)->toBeArray();
    expect($raws)->toHaveCount(5);
    expect($raws[0])->toHaveKeys(['name', 'email']);
});

it('can accept sequence', function () {
    $collection = FakeFactoryStub::new()
        ->count(3)
        ->sequence(
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        )
        ->make();

    expect($collection)->toHaveCount(3);
    expect($collection[0]->id)->toBe(1);
    expect($collection[1]->id)->toBe(2);
    expect($collection[2]->id)->toBe(3);
});

it('can use state', function () {
    $instance = FakeFactoryStub::new()
        ->withId(1)
        ->make();

    expect($instance->id)->toBe(1);
});

it('can set single attribute', function () {
    $instance = FakeFactoryStub::new()
        ->set('id', 1)
        ->make();

    expect($instance->id)->toBe(1);
});

it('throws error on create', function () {
    FakeFactoryStub::new()->create();
})->throws(TypeError::class);
