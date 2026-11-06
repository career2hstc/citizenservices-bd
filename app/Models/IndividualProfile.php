<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndividualProfile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'professional_title', 'profession', 'qualification', 'years_experience',
        'bio', 'website', 'verification_status', 'publication_status', 'location_id', 'address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
