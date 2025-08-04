<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated users are redirected from login page to dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/login');

    $response->assertRedirect(route('dashboard'));
});

test('authenticated users are redirected from registration page to dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/register');

    $response->assertRedirect(route('dashboard'));
});

test('authenticated users are redirected from forgot password page to dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/forgot-password');

    $response->assertRedirect(route('dashboard'));
});

test('authenticated users are redirected from home page to dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect(route('dashboard'));
});

test('guest users can access login page', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertViewIs('auth.login');
});

test('guest users can access registration page', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
    $response->assertViewIs('auth.register');
});
