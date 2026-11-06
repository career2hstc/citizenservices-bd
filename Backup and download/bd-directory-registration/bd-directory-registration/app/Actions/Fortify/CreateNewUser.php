<?php

namespace App\Actions\Fortify;

use App\Models\AccountProfile;
use App\Models\IndividualProfile;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Create a platform account and its initial profile atomically.
     * Privileged platform roles and verification states are never accepted
     * from the registration form.
     */
    public function create(array $input): User
    {
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'account_type' => ['required', 'string', 'in:customer,individual,organization'],
            'organization_name' => ['required_if:account_type,organization', 'nullable', 'string', 'max:255'],
            'organization_type' => ['required_if:account_type,organization', 'nullable', 'in:private,government,multinational,ngo,other'],
        ], [
            'organization_name.required_if' => 'Please enter your organization name.',
            'organization_type.required_if' => 'Please select your organization type.',
        ])->validate();

        return DB::transaction(function () use ($validated): User {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            AccountProfile::create([
                'user_id' => $user->id,
                'phone' => $validated['phone'] ?? null,
                'preferred_locale' => 'en',
                'is_active' => true,
            ]);

            // Every new account can use customer features. Provider roles are additive.
            $user->assignRole('customer');

            if ($validated['account_type'] === 'individual') {
                IndividualProfile::create([
                    'user_id' => $user->id,
                    'verification_status' => 'unverified',
                    'publication_status' => 'draft',
                ]);
                $user->assignRole('individual_provider');
            }

            if ($validated['account_type'] === 'organization') {
                $organizationName = $validated['organization_name'];
                $baseSlug = Str::slug($organizationName) ?: 'organization';
                $slug = $baseSlug;
                $suffix = 2;

                while (Organization::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $suffix++;
                }

                $organization = Organization::create([
                    'name' => $organizationName,
                    'slug' => $slug,
                    'organization_type' => $validated['organization_type'],
                    'verification_status' => 'unverified',
                    'publication_status' => 'draft',
                    'created_by' => $user->id,
                ]);

                $organization->members()->attach($user->id, [
                    'membership_role' => 'owner',
                    'membership_status' => 'active',
                    'joined_at' => now(),
                ]);

                $user->assignRole('organization_owner');
            }

            return $user;
        });
    }
}
