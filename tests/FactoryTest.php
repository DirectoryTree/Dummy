<?php

use DirectoryTree\Dummy\DummyData;
use DirectoryTree\Dummy\Tests\Fixtures\DataStatus;
use DirectoryTree\Dummy\Tests\Fixtures\FactoryClassStub;
use DirectoryTree\Dummy\Tests\Fixtures\FactoryStub;
use DirectoryTree\Dummy\Tests\Fixtures\FactoryWithConfigurationStub;
use DirectoryTree\Dummy\Tests\Fixtures\FactoryWithCustomClassStub;
use DirectoryTree\Dummy\Tests\Fixtures\FactoryWithStateStub;
use Illuminate\Support\Collection;

it('can generate single instance', function () {
    $instance = FactoryStub::new()->make();

    expect($instance)->toBeInstanceOf(DummyData::class);
});

it('can generate multiple instances in a collection', function () {
    $collection = FactoryStub::new()->count(5)->make();

    $collection->each(
        fn ($instance) => expect($instance)->toBeInstanceOf(DummyData::class)
    );

    expect($collection)->toHaveCount(5);
});

it('can accept attributes', function () {
    $instance = FactoryStub::new([
        'id' => 1,
        'name' => 'foo',
    ])->make();

    expect($instance->id)->toBe(1);
    expect($instance->name)->toBe('foo');
});

it('can interact with data attributes', function () {
    $data = new DummyData([
        0 => 'first',
        1 => '',
        'active' => 'true',
        'visits' => '5',
        'profile' => [
            'name' => 'Taylor',
        ],
    ]);

    expect($data['active'])->toBe('true');
    expect($data->exists(0))->toBeTrue();
    expect($data->has(0, 'profile.name'))->toBeTrue();
    expect($data->hasAny(['deleted_at', 0]))->toBeTrue();
    expect($data->has('profile.name'))->toBeTrue();
    expect($data->missing(2))->toBeTrue();
    expect($data->missing('deleted_at'))->toBeTrue();
    expect($data->filled(0))->toBeTrue();
    expect($data->filled('profile.name'))->toBeTrue();
    expect($data->notFilled(1))->toBeTrue();
    expect($data->notFilled('deleted_at'))->toBeTrue();
    expect($data->anyFilled(2, 0))->toBeTrue();
    expect($data->anyFilled(['deleted_at', 'profile.name']))->toBeTrue();
    expect($data->boolean('active'))->toBeTrue();
    expect($data->integer('visits'))->toBe(5);
    expect($data->get('profile.name'))->toBe('Taylor');
    expect($data->only('profile.name'))->toBe(['profile' => ['name' => 'Taylor']]);
    expect($data->except('visits'))->not->toHaveKey('visits');
    expect($data->collect(['active', 'visits']))->all()->toBe([
        'active' => 'true',
        'visits' => '5',
    ]);
});

it('can retrieve data as enums', function () {
    $data = new DummyData([
        'status' => 'active',
        'statuses' => ['active', 'inactive', 'missing'],
        'missing_status' => null,
    ]);

    expect($data->enum('status', DataStatus::class))->toBe(DataStatus::Active);
    expect($data->enum('missing_status', DataStatus::class, DataStatus::Inactive))->toBe(DataStatus::Inactive);
    expect($data->enum('missing_status', DataStatus::class, fn () => DataStatus::Inactive))->toBe(DataStatus::Inactive);
    expect($data->enum('status', stdClass::class, DataStatus::Inactive))->toBe(DataStatus::Inactive);
    expect($data->enums('statuses', DataStatus::class))->toBe([
        DataStatus::Active,
        DataStatus::Inactive,
    ]);
    expect($data->enums('missing_status', DataStatus::class))->toBe([]);
    expect($data->enums('statuses', stdClass::class))->toBe([]);
});

it('can accept invokable attribute callbacks', function () {
    $instance = FactoryStub::new(new class
    {
        public function __invoke(array $attributes): array
        {
            return [
                'name' => 'Invokable',
                'email' => $attributes['email'],
            ];
        }
    })->make();

    expect($instance->name)->toBe('Invokable');
});

it('can accept attribute closures', function () {
    $instance = FactoryStub::new()->make([
        'foo' => function (array $attributes) {
            expect($attributes)->toHaveKeys(['name', 'email']);

            return 'bar';
        },
    ]);

    expect($instance->foo)->toBe('bar');
});

it('does not invoke callable values returned by attribute closures', function () {
    $callback = fn () => 'bar';

    $instance = FactoryStub::new()->make([
        'foo' => fn () => $callback,
    ]);

    expect($instance->foo)->toBe($callback);
});

it('can make raw attributes', function () {
    $raw = FactoryStub::new()->count(5)->raw();

    expect($raw)->toBeArray();
    expect($raw[0])->toHaveKeys(['name', 'email']);
});

it('can make many raw attributes', function () {
    $raws = FactoryStub::new()->count(5)->raw();

    expect($raws)->toBeArray();
    expect($raws)->toHaveCount(5);
    expect($raws[0])->toHaveKeys(['name', 'email']);
});

it('can make a single instance after a count has been set', function () {
    $instance = FactoryStub::new()->count(5)->makeOne();

    expect($instance)->toBeInstanceOf(DummyData::class);
});

it('can make many instances from a number', function () {
    $collection = FactoryStub::new()->makeMany(3);

    expect($collection)->toBeInstanceOf(Collection::class);
    expect($collection)->toHaveCount(3);
});

it('can make many instances from state records', function () {
    $collection = FactoryStub::new()->makeMany([
        ['name' => 'Taylor'],
        ['name' => 'Nuno'],
    ]);

    expect($collection)->toHaveCount(2);
    expect($collection[0]->name)->toBe('Taylor');
    expect($collection[1]->name)->toBe('Nuno');
});

it('can accept sequence', function () {
    $collection = FactoryStub::new()
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
    $instance = FactoryStub::new()
        ->withId(1)
        ->make();

    expect($instance->id)->toBe(1);
});

it('can set single attribute', function () {
    $instance = FactoryStub::new()
        ->set('id', 1)
        ->make();

    expect($instance->id)->toBe(1);
});

it('can prepend state', function () {
    $instance = FactoryStub::new()
        ->state(['name' => 'Last'])
        ->prependState(['name' => 'First'])
        ->make();

    expect($instance->name)->toBe('Last');
});

it('can lazily make instances', function () {
    $lazy = FactoryStub::new()->lazy(['name' => 'Deferred']);

    expect($lazy)->toBeInstanceOf(Closure::class);
    expect($lazy()->name)->toBe('Deferred');
});

it('can create custom classes', function () {
    $instance = FactoryWithCustomClassStub::new()->make();

    expect($instance)->toBeInstanceOf(FactoryClassStub::class);
    expect($instance->name)->not->toBeNull();
    expect($instance->email)->not->toBeNull();
});

it('can create many custom classes', function () {
    $collection = FactoryWithCustomClassStub::new()
        ->count(5)
        ->make();

    expect($collection)->toBeInstanceOf(Collection::class);
    expect($collection)->toHaveCount(5);
    expect($collection->first())->toBeInstanceOf(FactoryClassStub::class);
});

it('can use state callbacks', function () {
    $instance = FactoryWithStateStub::new()
        ->admin()
        ->make();

    expect($instance->role)->toBe('admin');
    expect($instance->name)->toBe('Admin');
    expect($instance->email)->toBe('admin@example.com');
});

it('can use after making callbacks', function () {
    $instance = FactoryWithConfigurationStub::new()->make();

    expect($instance->name)->toBe('Custom');
});

it('can remove after making callbacks', function () {
    $instance = FactoryWithConfigurationStub::new()
        ->withoutAfterMaking()
        ->make(['name' => 'Original']);

    expect($instance->name)->toBe('Original');
});

it('can use macros', function () {
    FactoryStub::macro('named', function (string $name) {
        return $this->state(['name' => $name]);
    });

    try {
        $instance = FactoryStub::new()->named('Macro')->make();

        expect($instance->name)->toBe('Macro');
    } finally {
        FactoryStub::flushMacros();
    }
});

it('can build a factory with a count via times', function () {
    $collection = FactoryStub::times(3)->make();

    expect($collection)->toBeInstanceOf(Collection::class);
    expect($collection)->toHaveCount(3);
});

it('returns a single flat attributes array from raw when no count is set', function () {
    $raw = FactoryStub::new()->raw();

    expect($raw)->toBeArray();
    expect($raw)->toHaveKeys(['name', 'email']);
    expect($raw['name'])->toBeString();
});

it('accepts a callable for raw attributes', function () {
    $raw = FactoryStub::new()->raw(fn (array $attributes) => [
        'name' => 'Callable',
        'derived' => $attributes['email'],
    ]);

    expect($raw['name'])->toBe('Callable');
    expect($raw['derived'])->toBe($raw['email']);
});

it('returns an empty collection when make is called with a count below one', function () {
    $collection = FactoryStub::new()->count(0)->make();

    expect($collection)->toBeInstanceOf(Collection::class);
    expect($collection)->toBeEmpty();
});

it('sets the count from the sequence size via forEachSequence', function () {
    $collection = FactoryStub::new()
        ->forEachSequence(
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

it('uses a custom using callback to build instances', function () {
    $instance = FactoryStub::new()
        ->using(fn ($faker, array $attributes) => new FactoryClassStub(
            $attributes['name'],
            $attributes['email'],
        ))
        ->make();

    expect($instance)->toBeInstanceOf(FactoryClassStub::class);
    expect($instance->name)->toBeString();
    expect($instance->email)->toBeString();
});

it('exposes a faker generator instance', function () {
    expect(FactoryStub::new()->faker())->toBeInstanceOf(Faker\Generator::class);
});

it('throws when calling state methods on a factory without a bound class', function () {
    expect(fn () => FactoryStub::new()->admin())
        ->toThrow(BadMethodCallException::class, 'Cannot call state methods on a factory without a using class.');
});

it('throws when the state method does not exist on the bound class', function () {
    expect(fn () => FactoryWithCustomClassStub::new()->nonExistent())
        ->toThrow(BadMethodCallException::class);
});

it('applies state conditionally via when', function () {
    $applied = FactoryStub::new()
        ->when(true, fn ($factory) => $factory->set('flag', 'on'))
        ->make();

    $skipped = FactoryStub::new()
        ->when(false, fn ($factory) => $factory->set('flag', 'on'))
        ->make();

    expect($applied->flag)->toBe('on');
    expect($skipped->flag)->toBeNull();
});

it('applies state conditionally via unless', function () {
    $applied = FactoryStub::new()
        ->unless(false, fn ($factory) => $factory->set('flag', 'on'))
        ->make();

    $skipped = FactoryStub::new()
        ->unless(true, fn ($factory) => $factory->set('flag', 'on'))
        ->make();

    expect($applied->flag)->toBe('on');
    expect($skipped->flag)->toBeNull();
});

it('defaults makeMany to a single instance when no count or records are given', function () {
    $collection = FactoryStub::new()->makeMany();

    expect($collection)->toBeInstanceOf(Collection::class);
    expect($collection)->toHaveCount(1);
});

it('uses the existing count when makeMany receives no records', function () {
    $collection = FactoryStub::new()->count(4)->makeMany();

    expect($collection)->toHaveCount(4);
});

it('binds state closures to the factory instance', function () {
    $instance = FactoryStub::new()
        ->state(function () {
            return ['generated' => $this->faker()->name()];
        })
        ->make();

    expect($instance->generated)->toBeString()->not->toBeEmpty();
});
