<?php

use DirectoryTree\Fakeable\Tests\Fixtures\FactoryClassStub;
use DirectoryTree\Fakeable\Tests\Fixtures\FakeFactoryStub;
use DirectoryTree\Fakeable\Tests\Fixtures\FakeFactoryWithCustomClassStub;
use Illuminate\Support\Collection;
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

it('can accept attribute closures', function () {
    $instance = FakeFactoryStub::new()->make([
        'foo' => function (array $attributes) {
            expect($attributes)->toHaveKeys(['name', 'email']);

            return 'bar';
        },
    ]);

    expect($instance->foo)->toBe('bar');
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

it('can create custom classes', function () {
    $instance = FakeFactoryWithCustomClassStub::new()->make();

    expect($instance)->toBeInstanceOf(FactoryClassStub::class);
    expect($instance->name)->not->toBeNull();
    expect($instance->email)->not->toBeNull();
});

it('can create many custom classes', function () {
    $collection = FakeFactoryWithCustomClassStub::new()
        ->count(5)
        ->make();

    expect($collection)->toBeInstanceOf(Collection::class);
    expect($collection)->toHaveCount(5);
    expect($collection->first())->toBeInstanceOf(FactoryClassStub::class);
});
