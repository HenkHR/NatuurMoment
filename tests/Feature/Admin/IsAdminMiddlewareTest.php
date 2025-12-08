<?php

use App\Models\User;

test('admin routes require authentication', function () {
    $this->get('/admin/locations')
        ->assertRedirect('/login');
});

test('admin routes require admin user', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get('/admin/locations')
        ->assertStatus(403);
});

test('admin can access admin routes', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->get('/admin/locations')
        ->assertStatus(200);
});

test('admin link is visible for admin users', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    // Dashboard redirects to admin locations for logged-in users
    $this->actingAs($admin)
        ->get('/dashboard')
        ->assertRedirect('/admin/locations');
});

test('admin link is hidden for non-admin users', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertDontSee('href="/admin/locations"');
});
