# Registration and Email Verification Integration

This is an integration patch for an existing Laravel 12 + React + Inertia application using Laravel Fortify (the Laravel React starter kit authentication stack). It is not a standalone Laravel project.

## Before changing files

1. Back up the project and database.
2. Confirm the project uses Fortify: `composer show laravel/fortify`.
3. This package also includes the Phase 1 foundation migrations/models under `foundation/`. Install those first if they are not already installed. The registration action expects `account_profiles`, `organizations`, `organization_users`, and `individual_profiles`.
4. Confirm Spatie Permission is installed and its migrations have run.

If your existing app uses Breeze without Fortify, or a custom authentication controller, do not copy the Fortify action blindly; share `composer.json`, `routes/web.php`, `app/Models/User.php`, and the existing registration controller/action first so the integration can be adapted without duplicating routes.

## 1. Install the Phase 1 foundation (if not already installed)

Copy the contents of `foundation/database/migrations/` into your app's `database/migrations/` and `foundation/app/Models/` into `app/Models/`. Review `foundation/docs/USER_MODEL_SNIPPET.md` and merge its relationships into the existing User model. Do not overwrite your existing `User.php`.

If those migrations already exist and have run, do not copy or rerun them. Check `php artisan migrate:status` first. If the tables exist but Laravel does not show their migrations as run, stop and reconcile the migration history before proceeding; do not run duplicate create-table migrations.

## 2. Install / verify dependencies

```bash
composer require laravel/fortify
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Laravel\\Fortify\\FortifyServiceProvider" --tag="config"
php artisan vendor:publish --provider="Spatie\\Permission\\PermissionServiceProvider"
```

If these packages/config files already exist, do not publish over customized files. Keep the existing versions.

## 3. Install the action

Copy `app/Actions/Fortify/CreateNewUser.php` to the same path in your Laravel app.

In `app/Providers/FortifyServiceProvider.php`, ensure the action is registered:

```php
use App\Actions\Fortify\CreateNewUser;
use Laravel\Fortify\Fortify;

public function boot(): void
{
    Fortify::createUsersUsing(CreateNewUser::class);
    // Keep your existing Fortify configuration below.
}
```

Do not create a second FortifyServiceProvider if your project already has one.

## 4. Enable registration and email verification

In `config/fortify.php`, ensure the `features` array includes:

```php
Features::registration(),
Features::emailVerification(),
Features::resetPasswords(),
```

Keep existing features such as two-factor authentication if configured. Ensure `App\Providers\FortifyServiceProvider::class` is registered in `bootstrap/providers.php` if your app requires explicit provider registration.

## 5. Update the User model

Merge the following into the existing `app/Models/User.php`; do not replace the whole file. Preserve traits already used by your starter kit.

```php
use App\Models\AccountProfile;
use App\Models\IndividualProfile;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Traits\HasRoles;

// Add HasRoles to the existing class traits.

public function accountProfile(): HasOne
{
    return $this->hasOne(AccountProfile::class);
}

public function individualProfile(): HasOne
{
    return $this->hasOne(IndividualProfile::class);
}

public function organizations(): BelongsToMany
{
    return $this->belongsToMany(Organization::class, 'organization_users')
        ->withPivot(['membership_role', 'membership_status', 'invited_by', 'joined_at'])
        ->withTimestamps();
}
```

For email verification, the User model must implement `Illuminate\Contracts\Auth\MustVerifyEmail`. If it does not already, add the interface and the `Illuminate\Auth\MustVerifyEmail` trait (alias the trait if needed). Example:

```php
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use MustVerifyEmailTrait;
    // Keep the existing HasFactory, Notifiable, and other traits.
}
```

Some Laravel starter-kit versions already implement this; avoid adding the trait/interface twice.

## 6. Seed platform roles

Copy `database/seeders/PlatformRoleSeeder.php` into your app, then run:

```bash
php artisan db:seed --class=PlatformRoleSeeder
```

If your app uses a different role guard, align it consistently with your auth guard. Do not expose role assignment in the registration form.

## 7. Install the React registration page

Copy `resources/js/pages/auth/register.tsx` to the page path expected by your existing Inertia resolver. Laravel's current React starter kit commonly uses `resources/js/pages/auth/register.tsx`; other projects may use `resources/js/Pages/Auth/Register.jsx`.

The component posts to Fortify's existing `POST /register` endpoint. If your Inertia resolver uses a different naming convention, update the file path, not the URL.

## 8. Protect provider publishing routes

Merge the example in `routes/provider-protection.example.php` into `routes/web.php`. Apply `auth` and `verified` middleware to actions that publish or change provider listings. Keep the authenticated dashboard accessible to unverified users so they can reach the verification notice and resend link.

Fortify supplies the standard email verification routes when `Features::emailVerification()` is enabled. The user must be signed in to access the verification notice, verification link and resend action.

## 9. Configure email delivery

Set valid SMTP credentials in `.env` (use your email provider's values):

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Do not commit real SMTP passwords. For local testing, use `MAIL_MAILER=log` and inspect `storage/logs/laravel.log`; this logs verification links instead of sending email.

## 10. Clear caches and build

```bash
php artisan optimize:clear
php artisan migrate --pretend
php artisan migrate
npm install
npm run build
php artisan route:list --name=verification
php artisan route:list --path=register
```

Run `migrate --pretend` first to inspect pending schema changes. This patch itself adds no migrations; it depends on the Phase 1 foundation migrations.

## 11. Test

```bash
php artisan db:seed --class=PlatformRoleSeeder
php artisan test --filter=RegistrationTest
```

The feature tests expect the Phase 1 tables, Fortify registration routes, Spatie Permission tables, and a test database configured for migrations.

## Registration behavior

- `customer`: creates a user and account profile; assigns `customer`.
- `individual`: creates a user, account profile and draft individual profile; assigns `customer` and `individual_provider`.
- `organization`: creates a user, account profile, draft/unverified organization and owner membership; assigns `customer` and `organization_owner`.
- Email verification is handled by Fortify/Laravel's `Registered` event and verification notification.
- No role, verification status, or approval status is accepted from browser input.
- Organization verification remains a separate administrator workflow.

## Important note about an existing database

Do not run `migrate:fresh` on a database containing data. Back up the database before running migrations. If the Phase 1 tables do not exist yet, install the Phase 1 migrations first and review their foreign-key dependencies before migrating.
