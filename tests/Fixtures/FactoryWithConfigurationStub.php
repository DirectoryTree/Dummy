<?php

namespace DirectoryTree\Dummy\Tests\Fixtures;

use DirectoryTree\Dummy\Factory;
use Illuminate\Support\Fluent;

class FactoryWithConfigurationStub extends Factory
{
    protected function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
        ];
    }

    protected function configure(): static
    {
        return $this->afterMaking(function (Fluent $fluent) {
            $fluent->name = 'Custom';
        });
    }
}
