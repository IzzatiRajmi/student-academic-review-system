<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /** Get a seeded user by username. */
    protected function user(string $username): User
    {
        return User::where('username', $username)->firstOrFail();
    }
}
