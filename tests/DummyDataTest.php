<?php

use DirectoryTree\Dummy\DummyData;
use DirectoryTree\Dummy\Tests\Fixtures\DataStatus;
use Illuminate\Support\Collection;

it('can be constructed with an array', function () {
    $data = new DummyData(['foo' => 'bar']);

    expect($data->all())->toBe(['foo' => 'bar']);
});

it('can be constructed with any iterable', function () {
    $data = new DummyData(new ArrayIterator(['foo' => 'bar']));

    expect($data->all())->toBe(['foo' => 'bar']);
});

it('can be constructed empty', function () {
    expect((new DummyData)->all())->toBe([]);
});

it('can set attributes using dot notation', function () {
    $data = new DummyData;

    $data->set('profile.name', 'Taylor');

    expect($data->get('profile'))->toBe(['name' => 'Taylor']);
});

it('returns itself when setting an attribute', function () {
    $data = new DummyData;

    expect($data->set('foo', 'bar'))->toBe($data);
});

it('can get attributes using dot notation', function () {
    $data = new DummyData(['profile' => ['name' => 'Taylor']]);

    expect($data->get('profile.name'))->toBe('Taylor');
    expect($data->get('missing', 'default'))->toBe('default');
});

it('can get a raw value without dot notation parsing', function () {
    $data = new DummyData(['profile' => ['name' => 'Taylor']]);

    expect($data->value('profile'))->toBe(['name' => 'Taylor']);
    expect($data->value('profile.name'))->toBeNull();
    expect($data->value('missing', 'default'))->toBe('default');
});

it('returns the default value when fetching missing value', function () {
    $data = new DummyData;

    expect($data->value('missing', 'default'))->toBe('default');
    expect($data->value('missing', fn () => 'closure'))->toBe('closure');
});

it('can scope into a nested attribute as a new instance', function () {
    $data = new DummyData(['profile' => ['name' => 'Taylor']]);

    $scoped = $data->scope('profile');

    expect($scoped)->toBeInstanceOf(DummyData::class);
    expect($scoped)->not->toBe($data);
    expect($scoped->all())->toBe(['name' => 'Taylor']);
});

it('returns an empty scope when the key is missing', function () {
    expect((new DummyData)->scope('missing')->all())->toBe([]);
});

it('can return all attributes', function () {
    $data = new DummyData(['foo' => 'bar', 'baz' => 'qux']);

    expect($data->all())->toBe(['foo' => 'bar', 'baz' => 'qux']);
});

it('can return only specified keys via all', function () {
    $data = new DummyData(['foo' => 'bar', 'baz' => 'qux', 'profile' => ['name' => 'Taylor']]);

    expect($data->all(['foo', 'profile.name']))->toBe([
        'foo' => 'bar',
        'profile' => ['name' => 'Taylor'],
    ]);

    expect($data->all('foo', 'baz'))->toBe(['foo' => 'bar', 'baz' => 'qux']);
});

it('can check if attribute keys exist', function () {
    $data = new DummyData(['foo' => 'bar', 'profile' => ['name' => 'Taylor']]);

    expect($data->exists('foo'))->toBeTrue();
    expect($data->exists('profile.name'))->toBeTrue();
    expect($data->exists('missing'))->toBeFalse();
});

it('can check has with single, variadic, and array keys', function () {
    $data = new DummyData(['foo' => 'bar', 'baz' => 'qux']);

    expect($data->has('foo'))->toBeTrue();
    expect($data->has('foo', 'baz'))->toBeTrue();
    expect($data->has(['foo', 'baz']))->toBeTrue();
    expect($data->has('foo', 'missing'))->toBeFalse();
    expect($data->has(['foo', 'missing']))->toBeFalse();
});

it('can check hasAny with single, variadic, and array keys', function () {
    $data = new DummyData(['foo' => 'bar']);

    expect($data->hasAny('foo'))->toBeTrue();
    expect($data->hasAny('missing', 'foo'))->toBeTrue();
    expect($data->hasAny(['missing', 'foo']))->toBeTrue();
    expect($data->hasAny('missing', 'absent'))->toBeFalse();
    expect($data->hasAny(['missing', 'absent']))->toBeFalse();
});

it('can determine if attributes are missing', function () {
    $data = new DummyData(['foo' => 'bar']);

    expect($data->missing('missing'))->toBeTrue();
    expect($data->missing('foo'))->toBeFalse();
    expect($data->missing(['foo', 'missing']))->toBeTrue();
});

it('can determine if attributes are filled', function () {
    $data = new DummyData([
        'name' => 'Taylor',
        'empty' => '',
        'spaces' => '   ',
        'zero' => 0,
        'false' => false,
        'array' => [],
    ]);

    expect($data->filled('name'))->toBeTrue();
    expect($data->filled('zero'))->toBeTrue();
    expect($data->filled('false'))->toBeTrue();
    expect($data->filled('array'))->toBeTrue();
    expect($data->filled('empty'))->toBeFalse();
    expect($data->filled('spaces'))->toBeFalse();
    expect($data->filled('missing'))->toBeFalse();
    expect($data->filled('name', 'empty'))->toBeFalse();
    expect($data->filled(['name', 'zero']))->toBeTrue();
});

it('can determine if attributes are not filled', function () {
    $data = new DummyData(['name' => 'Taylor', 'empty' => '']);

    expect($data->isNotFilled('empty'))->toBeTrue();
    expect($data->isNotFilled('name'))->toBeFalse();
    expect($data->notFilled('empty'))->toBeTrue();
    expect($data->notFilled('name'))->toBeFalse();
});

it('can determine if any of the given keys are filled', function () {
    $data = new DummyData(['name' => 'Taylor', 'empty' => '']);

    expect($data->anyFilled('empty', 'name'))->toBeTrue();
    expect($data->anyFilled(['empty', 'name']))->toBeTrue();
    expect($data->anyFilled('empty', 'missing'))->toBeFalse();
    expect($data->anyFilled(['empty', 'missing']))->toBeFalse();
});

it('can retrieve a value as a boolean', function () {
    $data = new DummyData([
        'true' => 'true',
        'one' => '1',
        'yes' => 'yes',
        'on' => 'on',
        'false' => 'false',
        'zero' => '0',
        'no' => 'no',
        'off' => 'off',
    ]);

    expect($data->boolean('true'))->toBeTrue();
    expect($data->boolean('one'))->toBeTrue();
    expect($data->boolean('yes'))->toBeTrue();
    expect($data->boolean('on'))->toBeTrue();
    expect($data->boolean('false'))->toBeFalse();
    expect($data->boolean('zero'))->toBeFalse();
    expect($data->boolean('no'))->toBeFalse();
    expect($data->boolean('off'))->toBeFalse();
    expect($data->boolean('missing'))->toBeFalse();
    expect($data->boolean('missing', true))->toBeTrue();
});

it('can retrieve a value as an integer', function () {
    $data = new DummyData(['visits' => '5', 'foo' => 'bar']);

    expect($data->integer('visits'))->toBe(5);
    expect($data->integer('foo'))->toBe(0);
    expect($data->integer('missing'))->toBe(0);
    expect($data->integer('missing', 10))->toBe(10);
});

it('can retrieve a value as a float', function () {
    $data = new DummyData(['price' => '1.50']);

    expect($data->float('price'))->toBe(1.5);
    expect($data->float('missing'))->toBe(0.0);
    expect($data->float('missing', 2.5))->toBe(2.5);
});

it('can retrieve a value as an array', function () {
    $data = new DummyData([
        'items' => ['a', 'b'],
        'name' => 'Taylor',
        'profile' => ['email' => 'taylor@example.com'],
    ]);

    expect($data->array('items'))->toBe(['a', 'b']);
    expect($data->array('name'))->toBe(['Taylor']);
    expect($data->array(['name', 'profile.email']))->toBe([
        'name' => 'Taylor',
        'profile' => ['email' => 'taylor@example.com'],
    ]);
});

it('can retrieve a value as a collection', function () {
    $data = new DummyData([
        'items' => ['a', 'b'],
        'name' => 'Taylor',
    ]);

    expect($data->collect('items'))->toBeInstanceOf(Collection::class)->all()->toBe(['a', 'b']);
    expect($data->collect(['name']))->all()->toBe(['name' => 'Taylor']);
});

it('can retrieve a value as an enum', function () {
    $data = new DummyData([
        'status' => 'active',
        'invalid' => 'unknown',
        'empty' => '',
    ]);

    expect($data->enum('status', DataStatus::class))->toBe(DataStatus::Active);
    expect($data->enum('invalid', DataStatus::class))->toBeNull();
    expect($data->enum('invalid', DataStatus::class, DataStatus::Inactive))->toBe(DataStatus::Inactive);
    expect($data->enum('missing', DataStatus::class))->toBeNull();
    expect($data->enum('empty', DataStatus::class))->toBeNull();
    expect($data->enum('status', stdClass::class))->toBeNull();
});

it('can retrieve values as an array of enums', function () {
    $data = new DummyData([
        'statuses' => ['active', 'inactive', 'missing'],
        'empty' => [],
        'missing' => null,
    ]);

    expect($data->enums('statuses', DataStatus::class))->toBe([
        DataStatus::Active,
        DataStatus::Inactive,
    ]);

    expect($data->enums('empty', DataStatus::class))->toBe([]);
    expect($data->enums('missing', DataStatus::class))->toBe([]);
    expect($data->enums('statuses', stdClass::class))->toBe([]);
});

it('can return only specified keys', function () {
    $data = new DummyData([
        'name' => 'Taylor',
        'email' => 'taylor@example.com',
        'profile' => ['age' => 30],
    ]);

    expect($data->only(['name', 'profile.age']))->toBe([
        'name' => 'Taylor',
        'profile' => ['age' => 30],
    ]);

    expect($data->only('name', 'email'))->toBe([
        'name' => 'Taylor',
        'email' => 'taylor@example.com',
    ]);

    expect($data->only('missing'))->toBe([]);
});

it('can return all attributes except given keys', function () {
    $data = new DummyData([
        'name' => 'Taylor',
        'email' => 'taylor@example.com',
        'profile' => ['age' => 30],
    ]);

    expect($data->except(['name']))->toBe([
        'email' => 'taylor@example.com',
        'profile' => ['age' => 30],
    ]);

    expect($data->except('name', 'email'))->toBe([
        'profile' => ['age' => 30],
    ]);

    expect($data->except('profile.age'))->toBe([
        'name' => 'Taylor',
        'email' => 'taylor@example.com',
        'profile' => [],
    ]);
});

it('can return raw attributes via getAttributes', function () {
    $data = new DummyData(['foo' => 'bar']);

    expect($data->getAttributes())->toBe(['foo' => 'bar']);
});

it('can convert to an array', function () {
    $data = new DummyData(['foo' => 'bar']);

    expect($data->toArray())->toBe(['foo' => 'bar']);
});

it('can convert to JSON', function () {
    $data = new DummyData(['foo' => 'bar']);

    expect($data->toJson())->toBe('{"foo":"bar"}');
    expect($data->toJson(JSON_PRETTY_PRINT))->toContain("\n");
});

it('can be json serialized', function () {
    $data = new DummyData(['foo' => 'bar']);

    expect($data->jsonSerialize())->toBe(['foo' => 'bar']);
    expect(json_encode($data))->toBe('{"foo":"bar"}');
});

it('can be iterated', function () {
    $data = new DummyData(['foo' => 'bar', 'baz' => 'qux']);

    expect(iterator_to_array($data))->toBe(['foo' => 'bar', 'baz' => 'qux']);
});

it('supports array access', function () {
    $data = new DummyData(['foo' => 'bar']);

    expect(isset($data['foo']))->toBeTrue();
    expect(isset($data['missing']))->toBeFalse();
    expect($data['foo'])->toBe('bar');

    $data['baz'] = 'qux';
    expect($data['baz'])->toBe('qux');

    unset($data['foo']);
    expect(isset($data['foo']))->toBeFalse();
});

it('supports magic property access', function () {
    $data = new DummyData(['foo' => 'bar']);

    expect(isset($data->foo))->toBeTrue();
    expect(isset($data->missing))->toBeFalse();
    expect($data->foo)->toBe('bar');
    expect($data->missing)->toBeNull();

    $data->baz = 'qux';
    expect($data->baz)->toBe('qux');

    unset($data->foo);
    expect(isset($data->foo))->toBeFalse();
});

it('sets attributes via magic method calls', function () {
    $data = new DummyData;

    expect($data->name('Taylor'))->toBe($data);
    expect($data->active())->toBe($data);

    expect($data->name)->toBe('Taylor');
    expect($data->active)->toBeTrue();
});

it('uses the first parameter when multiple are passed to a magic call', function () {
    $data = new DummyData;

    $data->name('Taylor', 'ignored');

    expect($data->name)->toBe('Taylor');
});

it('returns null from value when the attribute exists with a null value', function () {
    $data = new DummyData(['foo' => null]);

    expect($data->value('foo', 'default'))->toBeNull();
    expect($data->foo)->toBeNull();
});

it('returns the default from scope when the key is missing', function () {
    $scoped = (new DummyData)->scope('missing', ['name' => 'Taylor']);

    expect($scoped)->toBeInstanceOf(DummyData::class);
    expect($scoped->all())->toBe(['name' => 'Taylor']);
});

it('supports integer keys for set and get', function () {
    $data = new DummyData;

    $data->set(0, 'zero');
    $data->set(1, 'one');

    expect($data->get(0))->toBe('zero');
    expect($data->get(1))->toBe('one');
    expect($data->all())->toBe([0 => 'zero', 1 => 'one']);
});

it('returns an empty array when array is called with a missing key', function () {
    expect((new DummyData)->array('missing'))->toBe([]);
});

it('returns an empty collection when collect is called with a missing key', function () {
    $collection = (new DummyData)->collect('missing');

    expect($collection)->toBeInstanceOf(Collection::class);
    expect($collection->all())->toBe([]);
});

it('can retrieve native boolean and null values as a boolean', function () {
    $data = new DummyData([
        'true' => true,
        'false' => false,
        'null' => null,
    ]);

    expect($data->boolean('true'))->toBeTrue();
    expect($data->boolean('false'))->toBeFalse();
    expect($data->boolean('null'))->toBeFalse();
});

it('resolves a closure default when an enum cannot be resolved', function () {
    $data = new DummyData(['status' => 'unknown']);

    expect($data->enum('status', DataStatus::class, fn () => DataStatus::Inactive))
        ->toBe(DataStatus::Inactive);

    expect($data->enum('missing', DataStatus::class, fn () => DataStatus::Active))
        ->toBe(DataStatus::Active);
});

it('sets false via magic call without coercing to true', function () {
    $data = new DummyData;

    $data->active(false);

    expect($data->active)->toBeFalse();
});

it('returns an empty array when only or except receive an empty input', function () {
    $data = new DummyData(['foo' => 'bar', 'baz' => 'qux']);

    expect($data->only([]))->toBe([]);
    expect($data->except([]))->toBe(['foo' => 'bar', 'baz' => 'qux']);
});

it('encodes nested structures to JSON', function () {
    $data = new DummyData([
        'profile' => ['name' => 'Taylor', 'roles' => ['admin', 'editor']],
    ]);

    expect($data->toJson())->toBe('{"profile":{"name":"Taylor","roles":["admin","editor"]}}');
});
