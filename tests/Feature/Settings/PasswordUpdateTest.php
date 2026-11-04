<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_password_can_be_changed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('settings.password.update'), [
                'current_password' => 'password',
                'password' => 'New-Secure-Pass-2026!',
                'password_confirmation' => 'New-Secure-Pass-2026!',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('settings.password.edit'));

        $this->assertTrue(Hash::check('New-Secure-Pass-2026!', $user->fresh()->password));
    }

    public function test_the_current_password_must_be_correct(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('settings.password.update'), [
                'current_password' => 'not-my-password',
                'password' => 'New-Secure-Pass-2026!',
                'password_confirmation' => 'New-Secure-Pass-2026!',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_weak_new_passwords_are_rejected(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('settings.password.update'), [
                'current_password' => 'password',
                'password' => '12345678',
                'password_confirmation' => '12345678',
            ])
            ->assertSessionHasErrors('password');
    }
}
