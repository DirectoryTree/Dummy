<?php

namespace DirectoryTree\Dummy\Tests\Fixtures;

use DirectoryTree\Dummy\Factory;

class FactoryWithCustomClassStub extends Factory
{
    protected function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
        ];
    }

    protected function generate(array $attributes = []): FactoryClassStub
    {
        return new FactoryClassStub(
            $attributes['name'],
            $attributes['email'],
        );
    }
}
