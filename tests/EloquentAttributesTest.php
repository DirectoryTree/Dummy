<?php

use DirectoryTree\Dummy\Tests\Fixtures\EloquentFactoryStub;
use DirectoryTree\Dummy\Tests\Fixtures\EloquentModelStub;
use DirectoryTree\Dummy\Tests\Fixtures\FactoryStub;
use DirectoryTree\Dummy\Tests\Fixtures\HasDummyFactoryWithEloquentAttributesStub;

it('expands eloquent factories and models in factory attributes', function () {
    $instance = FactoryStub::new()->make([
        'model_id' => new EloquentModelStub(123),
        'factory_id' => new EloquentFactoryStub(456),
        'dependent_id' => fn (array $attributes) => new EloquentModelStub($attributes['factory_id'] + 1),
    ]);

    expect($instance->model_id)->toBe(123);
    expect($instance->factory_id)->toBe(456);
    expect($instance->dependent_id)->toBe(457);
});

it('expands eloquent factories and models when making raw attributes', function () {
    $attributes = FactoryStub::new()->raw([
        'model_id' => new EloquentModelStub(123),
        'factory_id' => new EloquentFactoryStub(456),
    ]);

    expect($attributes['model_id'])->toBe(123);
    expect($attributes['factory_id'])->toBe(456);
});

it('expands eloquent factories and models in has factory definitions', function () {
    $instance = HasDummyFactoryWithEloquentAttributesStub::dummy()->make();

    expect($instance->model_id)->toBe(123);
    expect($instance->factory_id)->toBe(456);
    expect($instance->dependent_id)->toBe(457);
});

it('expands eloquent attributes after state overrides are applied', function () {
    $instance = HasDummyFactoryWithEloquentAttributesStub::dummy([
        'factory_id' => new EloquentFactoryStub(789),
    ])->make();

    expect($instance->factory_id)->toBe(789);
    expect($instance->dependent_id)->toBe(790);
});

it('expands has factory attributes with factory definition context', function () {
    $instance = HasDummyFactoryWithEloquentAttributesStub::dummy([
        'dependent_id' => fn (array $attributes) => new EloquentModelStub($attributes['factory_id'] + 2),
    ])->make();

    expect($instance->factory_id)->toBe(456);
    expect($instance->dependent_id)->toBe(458);
});
