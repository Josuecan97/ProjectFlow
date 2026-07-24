<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\LogoutController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Volt::route('/login', 'auth.login')->middleware('guest')->name('login');
Volt::route('/register', 'auth.register')->middleware('guest')->name('register');
Volt::route('/forgot-password', 'auth.forgot-password')
    ->middleware('guest')
    ->name('password.request');
Volt::route('/reset-password/{token}', 'auth.reset-password')
    ->middleware('guest')
    ->name('password.reset');
Volt::route('/invitations/{token}', 'organizations.invitation')
    ->name('invitations.show');

Volt::route('/verify-email', 'auth.verify-email')
    ->middleware('auth')
    ->name('verification.notice');
Route::get('/verify-email/{id}/{hash}', EmailVerificationController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');
Route::post('/email/verification-notification', EmailVerificationNotificationController::class)
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');
Route::post('/logout', LogoutController::class)
    ->middleware('auth')
    ->name('logout');

Volt::route('/dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'organization'])
    ->name('dashboard');
Volt::route('/profile', 'profile')
    ->middleware(['auth', 'verified'])
    ->name('profile.edit');

Volt::route('/organizations/create', 'organizations.create')
    ->middleware(['auth', 'verified'])
    ->name('organizations.create');
Volt::route('/organizations/select', 'organizations.select')
    ->middleware(['auth', 'verified'])
    ->name('organizations.select');
Volt::route('/organization/settings', 'organizations.settings')
    ->middleware(['auth', 'verified', 'organization'])
    ->name('organization.settings');
Volt::route('/organization/members', 'organizations.members')
    ->middleware(['auth', 'verified', 'organization'])
    ->name('organization.members');
Volt::route('/organization/roles', 'organizations.roles')
    ->middleware(['auth', 'verified', 'organization'])
    ->name('organization.roles');
Volt::route('/organization/subscription', 'organizations.subscription')
    ->middleware(['auth', 'verified', 'organization'])
    ->name('organization.subscription');

Volt::route('/people', 'people.index')
    ->middleware(['auth', 'verified', 'organization'])
    ->name('people.index');
Volt::route('/people/create', 'people.create')
    ->middleware(['auth', 'verified', 'organization'])
    ->name('people.create');
Volt::route('/people/{person}/edit', 'people.edit')
    ->middleware(['auth', 'verified', 'organization'])
    ->name('people.edit');
Volt::route('/people/{person}', 'people.show')
    ->middleware(['auth', 'verified', 'organization'])
    ->name('people.show');
