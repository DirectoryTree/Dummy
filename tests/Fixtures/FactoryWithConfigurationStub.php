<?php

namespace DirectoryTree\Dummy\Tests\Fixtures;

use DirectoryTree\Dummy\Data;
use DirectoryTree\Dummy\Factory;

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
        return $this->afterMaking(function (Data $data) {
            $data->name = 'Custom';
        });
    }
}
