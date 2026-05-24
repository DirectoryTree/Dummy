<p align="center">
<img src="https://github.com/DirectoryTree/Dummy/blob/master/art/logo.svg" width="250">
</p>

<p align="center">
Generate PHP class instances populated with fake dummy data using <a href="https://github.com/FakerPHP/Faker" target="_blank">Faker</a>
</p>

<p align="center">
<a href="https://github.com/directorytree/dummy/actions" target="_blank"><img src="https://img.shields.io/github/actions/workflow/status/directorytree/dummy/run-tests.yml?branch=master&style=flat-square"/></a>
<a href="https://packagist.org/packages/directorytree/dummy" target="_blank"><img src="https://img.shields.io/packagist/v/directorytree/dummy.svg?style=flat-square"/></a>
<a href="https://packagist.org/packages/directorytree/dummy" target="_blank"><img src="https://img.shields.io/packagist/dt/directorytree/dummy.svg?style=flat-square"/></a>
<a href="https://packagist.org/packages/directorytree/dummy" target="_blank"><img src="https://img.shields.io/packagist/l/directorytree/dummy.svg?style=flat-square"/></a>
</p>

---

## Index

- [Requirements](#requirements)
- [Installation](#installation)
- [Upgrading](#upgrading)
- [Introduction](#introduction)
- [Setup](#setup)
  - [HasDummyFactory Trait](#hasdummyfactory-trait)
  - [Class Factory](#class-factory)
- [Usage](#usage)
  - [Factory States](#factory-states)
  - [Eloquent Attributes](#eloquent-attributes)
  - [Factory Callbacks](#factory-callbacks)
  - [Factory Sequences](#factory-sequences)
  - [Factory Collections](#factory-collections)
  - [Factory Macros](#factory-macros)
  - [IDE Type Inference](#ide-type-inference)

## Requirements

- PHP >= 8.0

## Installation

You can install the package via composer:

```bash
composer require directorytree/dummy --dev
```

## Upgrading

### From v1 to v2

Dummy v2 renames the trait API so it is explicitly Dummy-owned and does not reserve the common `factory()` method name on your classes.

1. Replace `DirectoryTree\Dummy\HasFactory` with `DirectoryTree\Dummy\HasDummyFactory`.
2. Replace `use HasFactory;` with `use HasDummyFactory;`.
3. Replace `YourClass::factory()` calls with `YourClass::dummy()`.
4. Rename `toFactoryInstance` to `toDummyInstance`.
5. Rename `getFactoryDefinition` to `getDummyDefinition`.
6. Change the `toDummyInstance` argument from `array` to `DirectoryTree\Dummy\DummyData`.

Before:

```php
use DirectoryTree\Dummy\HasFactory;

class Reservation
{
    use HasFactory;

    protected static function toFactoryInstance(array $attributes): static
    {
        return new static(
            $attributes['name'],
            $attributes['email'],
        );
    }
}

$reservation = Reservation::factory()->make();
```

After:

```php
use DirectoryTree\Dummy\DummyData;
use DirectoryTree\Dummy\HasDummyFactory;

class Reservation
{
    use HasDummyFactory;

    protected static function toDummyInstance(DummyData $attributes): static
    {
        return new static(
            $attributes['name'],
            $attributes['email'],
        );
    }
}

$reservation = Reservation::dummy()->make();
```

If your constructor or factory method needs a plain array, call `all()`:

```php
protected static function toDummyInstance(DummyData $attributes): static
{
    return new static($attributes->all());
}
```

You can also replace manual array access with helper methods where useful:

```php
$attributes->get('profile.name');
$attributes->filled('email');
$attributes->boolean('active');
$attributes->integer('visits');
$attributes->enum('status', Status::class);
$attributes->enums('roles', Role::class);
$attributes->only(['name', 'email']);
$attributes->except('password');
```

State callbacks, attribute closures, `raw()`, and custom `Factory::generate(array $attributes)` methods continue to receive plain arrays.

## Introduction

Consider you have a class representing a restaurant reservation:

```php
namespace App\Data;

class Reservation
{
    public function __construct(
        public string $name,
        public string $email,
        public DateTime $date,
    ) {}
}
```

To make dummy instances of this class during testing, you have to manually populate it with dummy data.

This can quickly get out of hand as your class grows, and you may find yourself writing the same dummy data generation code over and over again.

Dummy provides you with a simple way to generate dummy instances of your classes using a simple API:

```php
// Generate one instance:
$reservation = Reservation::dummy()->make();

// Generate multiple instances:
$collection = Reservation::dummy()->count(5)->make();
```

## Setup

Dummy provides you two different ways to generate classes with dummy data.

### HasDummyFactory Trait

The `HasDummyFactory` trait is applied directly to the class you would like to generate dummy instances of.

To use the `HasDummyFactory` trait, you must implement the `toDummyInstance` and `getDummyDefinition` methods:

```php
namespace App\Data;

use DateTime;
use Faker\Generator;
use DirectoryTree\Dummy\DummyData;
use DirectoryTree\Dummy\HasDummyFactory;

/**
 * @use HasDummyFactory<Reservation>
 */
class Reservation
{
    use HasDummyFactory;
    
    /**
     * Constructor.
     */
    public function __construct(
        public string $name,
        public string $email,
        public DateTime $date,
    ) {}
    
    /**
     * Define the factory's default state.
     */
    protected static function getDummyDefinition(Generator $faker): array
    {
        return [
            'name' => $faker->name(),
            'email' => $faker->email(),
            'datetime' => $faker->dateTime(),
        ];
    }
    
    /**
     * Create a new instance of the class using the factory definition.
     */
    protected static function toDummyInstance(DummyData $attributes): static
    {
        return new static(
            $attributes['name'],
            $attributes['email'],
            $attributes['datetime'],
        );
    }
}
```

The `$attributes` argument passed into `toDummyInstance` is a `DirectoryTree\Dummy\DummyData` instance. It supports array access and common data helpers, such as `get`, `has`, `filled`, `notFilled`, `boolean`, `integer`, `enum`, `enums`, `only`, `except`, `collect`, and `all`.

Once implemented, you may call the `Reservation::dummy()` method to create a new dummy factory:

```php
$factory = Reservation::dummy();
```

#### Dynamic State Methods

The `HasDummyFactory` trait supports defining dynamic state methods. You can define state methods in your class using the format `get{StateName}State` and call them dynamically on the factory:

```php
namespace App\Data;

use DateTime;
use Faker\Generator;
use DirectoryTree\Dummy\DummyData;
use DirectoryTree\Dummy\HasDummyFactory;

/**
 * @use HasDummyFactory<Reservation>
 */
class Reservation
{
    use HasDummyFactory;

    public function __construct(
        public string $name,
        public string $email,
        public DateTime $datetime,
        public string $status = 'pending',
        public string $type = 'standard',
    ) {}

    // Dynamic state methods...

    public static function getConfirmedState(): array
    {
        return ['status' => 'confirmed'];
    }

    public static function getPremiumState(): array
    {
        return [
            'type' => 'premium',
            'status' => 'confirmed',
        ];
    }

    public static function getCancelledState(): array
    {
        return ['status' => 'cancelled'];
    }

    protected static function toDummyInstance(DummyData $attributes): self
    {
        return new static(
            $attributes['name'],
            $attributes['email'],
            $attributes['datetime'],
            $attributes['status'] ?? 'pending',
            $attributes['type'] ?? 'standard',
        );
    }

    protected static function getDummyDefinition(Generator $faker): array
    {
        return [
            'name' => $faker->name(),
            'email' => $faker->email(),
            'datetime' => $faker->dateTime(),
        ];
    }
}
```

You can then use these state methods dynamically:

```php
// Create a confirmed reservation
$confirmed = Reservation::dummy()->confirmed()->make();

// Create a premium reservation
$premium = Reservation::dummy()->premium()->make();

// Chain multiple states
$premiumCancelled = Reservation::dummy()->premium()->cancelled()->make();
```

### Class Factory

If you need more control over the dummy data generation process, you may use the `Factory` class.

The `Factory` class is used to generate dummy instances of a class using a separate factory class definition.

To use the `Factory` class, you must extend it with your own and override the `definition` and `generate` methods:

```php
namespace App\Factories;

use App\Data\Reservation;
use DirectoryTree\Dummy\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the factory's default state.
     */
    protected function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'datetime' => $this->faker->dateTime(),
        ];
    }
    
    /**
     * Generate a new instance of the class.
     */
    protected function generate(array $attributes): Reservation
    {
        return new Reservation(
            $attributes['name'],
            $attributes['email'],
            $attributes['datetime'],
        );
    }
}
```

## Usage

Once you've defined a factory, you can generate dummy instances of your class using the `make` method:

```php
// Using the trait:
$reservation = Reservation::dummy()->make();

// Using the factory class:
$reservation = ReservationFactory::new()->make();
```

To add or override attributes in your definition, you may pass an array of attributes to the `make` method:

```php
$reservation = Reservation::dummy()->make([
    'name' => 'John Doe',
]);
```

To generate multiple instances of the class, you may use the `count` method:

> This will return an `Illuminate\Support\Collection` instance containing the generated classes.

```php
$collection = Reservation::dummy()->count(5)->make();
```

If you have a counted factory but need one instance, use `makeOne`:

```php
$reservation = Reservation::dummy()->count(5)->makeOne();
```

To make several instances with a different state record for each one, use `makeMany`:

```php
$collection = Reservation::dummy()->makeMany([
    ['name' => 'Taylor Otwell'],
    ['name' => 'Nuno Maduro'],
]);
```

You may also defer generation by creating a lazy callback:

```php
$makeReservation = Reservation::dummy()->lazy([
    'name' => 'John Doe',
]);

$reservation = $makeReservation();
```

### Factory States

State manipulation methods allow you to define discrete modifications
that can be applied to your dummy factories in any combination.

For example, your `App\Factories\Reservation` factory might contain a `tomorrow`
state method that modifies one of its default attribute values:

```php
class ReservationFactory extends Factory
{
    // ...

    /**
     * Indicate that the reservation is for tomorrow.
     */
    public function tomorrow(): Factory
    {
        return $this->state(function (array $attributes) {
            return ['datetime' => new DateTime('tomorrow')];
        });
    }
}
```

You may prepend a state when you need it evaluated before the factory's existing states:

```php
$reservation = Reservation::dummy()
    ->tomorrow()
    ->prependState([
        'name' => 'Early State',
    ])
    ->make();
```

### Eloquent Attributes

When Laravel's Eloquent is installed, Dummy will expand Eloquent model instances and factories into model keys:

```php
use App\Models\Company;
use App\Models\User;

$reservation = Reservation::dummy()->make([
    'company_id' => Company::factory(),
    'user_id' => User::factory()->create(),
]);
```

This also works for values returned from attribute closures, so dependent attributes can use previously expanded keys:

```php
$reservation = Reservation::dummy()->make([
    'company_id' => Company::factory(),
    'user_id' => fn (array $attributes) => User::factory([
        'company_id' => $attributes['company_id'],
    ]),
]);
```

### Factory Callbacks

Factory callbacks are registered using the `afterMaking` method and allow you to perform
additional tasks after making or creating a class. You should register these callbacks
by defining a `configure` method on your factory class. This method will be
automatically called when the factory is instantiated:

```php
class ReservationFactory extends Factory
{
    // ...
    
    /**
     * Configure the dummy factory.
     */
    protected function configure(): static
    {
        return $this->afterMaking(function (Reservation $reservation) {
            // ...
        });
    }
}
```

You may remove configured `afterMaking` callbacks for a single factory chain with `withoutAfterMaking`:

```php
$reservation = ReservationFactory::new()
    ->withoutAfterMaking()
    ->make();
```

### Factory Sequences

Sometimes you may wish to alternate the value of a given attribute for each generated
class.

You may accomplish this by defining a state transformation as a `sequence`:

```php
Reservation::dummy()
    ->count(3)
    ->sequence(
        ['datetime' => new Datetime('tomorrow')],
        ['datetime' => new Datetime('next week')],
        ['datetime' => new Datetime('next month')],
    )
    ->make();
```

### Factory Collections

By default, when making more than one dummy class, an instance of `Illuminate\Support\Collection` will be returned.

If you need to customize the collection of classes generated by a factory, you may override the `collect` method:

```php
class ReservationFactory extends Factory
{
    // ...
    
    /**
     * Create a new collection of classes.
     */
    public function collect(array $instances = []): ReservationCollection
    {
        return new ReservationCollection($instances);
    }
}
```

### Factory Macros

Factories are macroable, allowing you to register reusable factory helpers:

```php
use DirectoryTree\Dummy\Factory;

Factory::macro('named', function (string $name) {
    return $this->state([
        'name' => $name,
    ]);
});

$reservation = Reservation::dummy()->named('John Doe')->make();
```

### IDE Type Inference

Dummy includes generic PHPDoc annotations so static analysis tools and IDEs can infer factory return types.

When using the `HasDummyFactory` trait, add an `@use` annotation to your class:

```php
/**
 * @use HasDummyFactory<Reservation>
 */
class Reservation
{
    use HasDummyFactory;

    // ...
}
```

When using a dedicated factory class, add an `@extends` annotation:

```php
/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    // ...
}
```

These annotations allow tools to infer that `Reservation::dummy()->makeOne()` and `ReservationFactory::new()->makeOne()` return a `Reservation` instance.
