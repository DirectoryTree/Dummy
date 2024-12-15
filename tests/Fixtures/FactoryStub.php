<?php

namespace DirectoryTree\Dummy\Tests\Fixtures;

use DirectoryTree\Dummy\Factory;

class FactoryStub extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
        ];
    }

    public function withId(int $id): static
    {
        return $this->state(function () use ($id) {
            return ['id' => $id];
        });
    }
}
