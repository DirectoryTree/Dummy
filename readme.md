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
- [Introduction](#introduction)
- [Setup](#setup)
    - [HasFactory Trait](#hasfactory-trait)
    - [Class Factory](#class-factory)
- [Usage](#usage)
    - [Factory States](#factory-states)
    - [Factory Callbacks](#factory-callbacks)
    - [Factory Sequences](#factory-sequences)
    - [Factory Collections](#factory-collections)

## Requirements

- PHP >= 8.0

## Installation

You can install the package via composer:

```bash
composer require directorytree/dummy --dev
```

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
$reservation = Reservation::factory()->make();

// Generate multiple instances:
$collection = Reservation::factory()->count(5)->make();
```

## Setup

Dummy provides you two different ways to generate classes with dummy data.

### HasFactory Trait

The `HasFactory` trait is applied directly to the class you would like to generate dummy instances of.

To use the `HasFactory` trait, you must implement the `toFactoryInstance` and `getFactoryDefinition` methods:

```php
namespace App\Data;

use DateTime;
use Faker\Generator;
use DirectoryTree\Dummy\HasFactory;

class Reservation
{
    use HasFactory;
    
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
    protected function getFactoryDefinition(Generator $faker): array
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
    protected static function toFactoryInstance(array $attributes): static
    {
        return new static(
            $attributes['name'],
            $attributes['email'],
            $attributes['datetime'],
        );
    }
}
```

Once implemented, you may call the `Reservation::factory()` method to create a new dummy factory:

```php
$factory = Reservation::factory();
```

#### Dynamic State Methods

The `HasFactory` trait supports defining dynamic state methods. You can define state methods in your class using the format `get{StateName}State` and call them dynamically on the factory:

```php
namespace App\Data;

use DateTime;
use Faker\Generator;
use DirectoryTree\Dummy\HasFactory;

class Reservation
{
    use HasFactory;

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

    protected static function toFactoryInstance(array $attributes): self
    {
        return new static(
            $attributes['name'],
            $attributes['email'],
            $attributes['datetime'],
            $attributes['status'] ?? 'pending',
            $attributes['type'] ?? 'standard',
        );
    }

    protected static function getFactoryDefinition(Generator $faker): array
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
$confirmed = Reservation::factory()->confirmed()->make();

// Create a premium reservation
$premium = Reservation::factory()->premium()->make();

// Chain multiple states
$premiumCancelled = Reservation::factory()->premium()->cancelled()->make();
```

### Class Factory

If you need more control over the dummy data generation process, you may use the `Factory` class.

The `Factory` class is used to generate dummy instances of a class using a separate factory class definition.

To use the `Factory` class, you must extend it with your own and override the `definition` and `generate` methods:

```php
namespace App\Factories;

use App\Data\Reservation;
use DirectoryTree\Dummy\Factory;

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
$reservation = Reservation::factory()->make();

// Using the factory class:
$reservation = ReservationFactory::new()->make();
```

To add or override attributes in your definition, you may pass an array of attributes to the `make` method:

```php
$reservation = Reservation::factory()->make([
    'name' => 'John Doe',
]);
```

To generate multiple instances of the class, you may use the `count` method:

> This will return a `Illuminate\SupportCollection` instance containing the generated classes.

```php
$collection = Reservation::factory()->count(5)->make();
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

### Factory Sequences

Sometimes you may wish to alternate the value of a given attribute for each generated 
class. 

You may accomplish this by defining a state transformation as a `sequence`:

```php
Reservation::factory()
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
