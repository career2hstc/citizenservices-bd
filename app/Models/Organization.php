<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organization extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'organization_type', 'registration_number', 'established_year',
        'description', 'logo_path', 'website', 'public_email', 'public_phone',
        'verification_status', 'publication_status', 'created_by',
        'head_office_location_id', 'head_office_address',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_users')
            ->withPivot(['membership_role', 'membership_status', 'invited_by', 'joined_at'])
            ->withTimestamps();
    }

    public function headOfficeLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'head_office_location_id');
    }
}
