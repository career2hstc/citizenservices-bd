<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['customer', 'individual_provider', 'organization_owner'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    public function test_customer_can_register_and_receives_verification_email(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Test Customer',
            'email' => 'customer@example.test',
            'phone' => '01700000000',
            'account_type' => 'customer',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
        ]);

        $user = User::where('email', 'customer@example.test')->firstOrFail();

        $response->assertRedirect();
        $this->assertDatabaseHas('account_profiles', ['user_id' => $user->id]);
        $this->assertTrue($user->hasRole('customer'));
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_individual_can_register(): void
    {
        $this->post('/register', [
            'name' => 'Test Expert',
            'email' => 'expert@example.test',
            'account_type' => 'individual',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
        ])->assertRedirect();

        $user = User::where('email', 'expert@example.test')->firstOrFail();

        $this->assertDatabaseHas('individual_profiles', ['user_id' => $user->id]);
        $this->assertTrue($user->hasRole('customer'));
        $this->assertTrue($user->hasRole('individual_provider'));
    }

    public function test_organization_owner_can_register(): void
    {
        $this->post('/register', [
            'name' => 'Organization Owner',
            'email' => 'owner@example.test',
            'account_type' => 'organization',
            'organization_name' => 'Example Private Limited',
            'organization_type' => 'private',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
        ])->assertRedirect();

        $user = User::where('email', 'owner@example.test')->firstOrFail();
        $organization = Organization::where('name', 'Example Private Limited')->firstOrFail();

        $this->assertDatabaseHas('organization_users', [
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'membership_role' => 'owner',
        ]);
        $this->assertSame('unverified', $organization->verification_status);
        $this->assertTrue($user->hasRole('organization_owner'));
    }

    public function test_registration_rejects_privileged_role_input(): void
    {
        $this->from('/register')->post('/register', [
            'name' => 'Malicious Input',
            'email' => 'bad@example.test',
            'account_type' => 'customer',
            'role' => 'super_admin',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
        ])->assertSessionHasNoErrors();

        $user = User::where('email', 'bad@example.test')->firstOrFail();
        $this->assertFalse($user->hasRole('super_admin'));
    }
}
