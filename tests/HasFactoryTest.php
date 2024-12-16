<?php

use DirectoryTree\Dummy\Data;
use DirectoryTree\Dummy\Tests\Fixtures\HasFactoryInstanceStub;
use DirectoryTree\Dummy\Tests\Fixtures\HasFactoryStub;

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
