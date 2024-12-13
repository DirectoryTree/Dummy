<?php

namespace DirectoryTree\Fakeable\Tests\Fixtures;

use DirectoryTree\Fakeable\Factory;

class FakeFactoryWithCustomClassStub extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
        ];
    }

    protected function newClass(array $attributes = []): mixed
    {
        return new FactoryClassStub(
            $attributes['name'],
            $attributes['email'],
        );
    }
}
