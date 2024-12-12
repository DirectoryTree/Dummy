<?php

namespace DirectoryTree\Fakeable\Tests\Fixtures;

use DirectoryTree\Fakeable\Factory;

class FakeFactoryStub extends Factory
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
