<?php

namespace DirectoryTree\Dummy\Tests\Fixtures;

use DirectoryTree\Dummy\Factory;

class FactoryWithStateStub extends Factory
{
    protected function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
        ];
    }

    public function admin(): static
    {
        return $this->state(function () {
            return [
                'role' => 'admin',
                'name' => 'Admin',
                'email' => 'admin@example.com',
            ];
        });
    }
}
