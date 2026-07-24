<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Livewire\Volt\Volt;

it('sends a password reset link', function () {
    Notification::fake();
    $user = User::factory()->create();

    Volt::test('auth.forgot-password')
        ->set('email', $user->email)
        ->call('sendResetLink')
        ->assertHasNoErrors();

    Notification::assertSentTo($user, ResetPassword::class);
});

it('does not reveal whether an email exists', function () {
    Notification::fake();

    Volt::test('auth.forgot-password')
        ->set('email', 'missing@example.com')
        ->call('sendResetLink')
        ->assertHasNoErrors();

    Notification::assertNothingSent();
});
