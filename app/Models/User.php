<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    public const ROLE_STUDENT = 'student';
    public const ROLE_ADMIN = 'admin';

    protected $fillable = ['username', 'name', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function reviewRequests(): HasMany
    {
        return $this->hasMany(ReviewRequest::class, 'student_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /** "Syamimi" from "Syamimi binti Nadzri" (used in the welcome line). */
    public function firstName(): string
    {
        return explode(' ', trim($this->name))[0];
    }

    public function homeRoute(): string
    {
        return $this->isAdmin() ? 'admin.dashboard' : 'student.dashboard';
    }
}
