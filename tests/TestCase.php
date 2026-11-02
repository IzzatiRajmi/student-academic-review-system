<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /** Get a seeded user by the first part of their email, e.g. 'syamimi' or 'admin'. */
    protected function user(string $name): User
    {
        return User::where('email', 'like', $name.'@%')->firstOrFail();
    }
}
