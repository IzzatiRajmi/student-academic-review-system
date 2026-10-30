<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

/** TC01–TC07, TC17: login, logout and role access. */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_tc01_guest_is_redirected_to_login(): void
    {
        $this->get('/student/dashboard')->assertRedirect('/');
        $this->followingRedirects()->get('/student/dashboard')->assertSee('Please log in to continue.');
    }

    public function test_tc02_wrong_password_shows_error(): void
    {
        $this->post('/login', ['username' => 'syamimi', 'password' => 'wrong'])
            ->assertSessionHasErrors(['username' => 'Incorrect username or password.']);
        $this->assertGuest();
    }

    public function test_tc03_empty_login_shows_validation(): void
    {
        $this->post('/login', ['username' => '', 'password' => ''])
            ->assertSessionHasErrors(['username' => 'Please enter your username and password.']);
    }

    public function test_tc04_student_login_lands_on_student_dashboard(): void
    {
        $this->post('/login', ['username' => 'syamimi', 'password' => 'Student@123'])
            ->assertRedirect('/student/dashboard');

        $this->get('/student/dashboard')
            ->assertOk()
            ->assertSee('Welcome, Syamimi')
            ->assertViewHas('stats', ['total' => 3, 'pending' => 1, 'approved' => 2, 'rejected' => 0]);
    }

    public function test_tc05_login_locked_after_five_failed_attempts(): void
    {
        RateLimiter::clear('syamimi|127.0.0.1');
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['username' => 'syamimi', 'password' => "wrong{$i}"]);
        }

        $this->post('/login', ['username' => 'syamimi', 'password' => 'Student@123'])
            ->assertSessionHasErrors('username');
        $this->assertGuest();
        $this->assertStringContainsString('Too many failed attempts', session('errors')->first('username'));
    }

    public function test_tc06_student_cannot_open_admin_pages(): void
    {
        $this->actingAs($this->user('syamimi'))
            ->get('/admin/dashboard')
            ->assertRedirect('/student/dashboard')
            ->assertSessionHas('error', 'You do not have permission to access that page.');
    }

    public function test_tc07_admin_cannot_open_student_pages(): void
    {
        $this->actingAs($this->user('admin'))
            ->get('/student/requests')
            ->assertRedirect('/admin/dashboard')
            ->assertSessionHas('error');
    }

    public function test_tc17_logout_returns_to_login_with_message(): void
    {
        $this->actingAs($this->user('syamimi'))
            ->post('/logout')
            ->assertRedirect('/')
            ->assertSessionHas('success', 'You have been logged out.');

        $this->assertGuest();
        $this->get('/student/dashboard')->assertRedirect('/');
    }

    public function test_logged_in_user_visiting_login_goes_to_own_dashboard(): void
    {
        $this->actingAs($this->user('admin'))->get('/')->assertRedirect('/admin/dashboard');
    }
}
