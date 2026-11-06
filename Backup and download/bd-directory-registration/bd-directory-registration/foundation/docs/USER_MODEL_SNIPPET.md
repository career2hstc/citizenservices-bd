# Add relationships and Spatie roles to `app/Models/User.php`

Keep the traits already present in the Laravel React starter kit. Add `HasRoles` to the imports and class traits, then add these relationships:

```php
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// Inside User class, alongside existing traits:
use HasRoles;

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

Do not replace the whole starter-kit `User.php`; merge these imports, trait and methods into the existing class.
