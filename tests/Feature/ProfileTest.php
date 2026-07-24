<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Livewire\Volt\Volt;

it('updates the authenticated user profile', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->actingAs($user);

    Volt::test('profile')
        ->set('name', 'Nombre actualizado')
        ->set('email', 'nuevo@example.com')
        ->call('updateProfile')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toBe('Nombre actualizado')
        ->and($user->email)->toBe('nuevo@example.com')
        ->and($user->hasVerifiedEmail())->toBeFalse();

    Notification::assertSentTo($user, VerifyEmail::class);
});
