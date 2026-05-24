<?php

namespace DirectoryTree\Dummy\Tests\Fixtures;

use DirectoryTree\Dummy\DummyData;
use DirectoryTree\Dummy\HasDummyFactory;
use Faker\Generator;

class HasDummyFactoryWithStatesStub
{
    use HasDummyFactory;

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

    protected static function toDummyInstance(DummyData $attributes): DummyData
    {
        return $attributes;
    }

    protected static function getDummyDefinition(Generator $faker): array
    {
        return [
            'name' => $faker->name(),
            'email' => $faker->email(),
            'role' => 'user',
            'status' => 'active',
        ];
    }
}
