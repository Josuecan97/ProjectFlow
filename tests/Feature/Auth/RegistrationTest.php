<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Livewire\Volt\Volt;

it('registers a user and requests email verification', function () {
    Notification::fake();

    Volt::test('auth.register')
        ->set('name', 'Ada Lovelace')
        ->set('email', 'ada@example.com')
        ->set('password', 'SecurePassword123!')
        ->set('password_confirmation', 'SecurePassword123!')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('verification.notice', absolute: false));

    $user = User::query()->where('email', 'ada@example.com')->firstOrFail();

    $this->assertAuthenticatedAs($user);
    expect($user->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($user, VerifyEmail::class);
});

it('requires unique email addresses', function () {
    $user = User::factory()->create();

    Volt::test('auth.register')
        ->set('name', 'Otro usuario')
        ->set('email', $user->email)
        ->set('password', 'SecurePassword123!')
        ->set('password_confirmation', 'SecurePassword123!')
        ->call('register')
        ->assertHasErrors('email');
});
