<?php

namespace DirectoryTree\Dummy\Tests\Fixtures;

use DirectoryTree\Dummy\Data;
use DirectoryTree\Dummy\HasFactory;
use Faker\Generator;

class HasFactoryWithStatesStub
{
    use HasFactory;

    protected static function toFactoryInstance(array $attributes): Data
    {
        return new Data($attributes);
    }

    protected static function getFactoryDefinition(Generator $faker): array
    {
        return [
            'name' => $faker->name(),
            'email' => $faker->email(),
            'role' => 'user',
            'status' => 'active',
        ];
    }

    /**
     * Admin state method.
     */
    public static function getAdminState(): array
    {
        return [
            'role' => 'admin',
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ];
    }

    /**
     * Inactive state method.
     */
    public static function getInactiveState(): array
    {
        return [
            'status' => 'inactive',
        ];
    }

    /**
     * Premium state method.
     */
    public static function getPremiumState(): array
    {
        return [
            'role' => 'premium',
            'subscription' => 'premium',
        ];
    }
}
