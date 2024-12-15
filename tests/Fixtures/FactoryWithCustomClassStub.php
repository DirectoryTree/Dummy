<?php

namespace DirectoryTree\Dummy\Tests\Fixtures;

use DirectoryTree\Dummy\Factory;

class FactoryWithCustomClassStub extends Factory
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
