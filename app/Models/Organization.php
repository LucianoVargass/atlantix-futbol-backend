<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organization extends Model
{
    protected $fillable = ['name', 'slug', 'plan', 'branding', 'active'];

    protected $casts = [
        'branding' => 'array',
        'active' => 'boolean',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(OrganizationMember::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_members')->withPivot('role');
    }

    public function tournaments(): HasMany
    {
        return $this->hasMany(Tournament::class);
    }

    public function memberRole(?int $userId): ?string
    {
        if (!$userId) {
            return null;
        }
        return $this->members()->where('user_id', $userId)->value('role');
    }

    public function hasMember(?int $userId): bool
    {
        return $this->memberRole($userId) !== null;
    }
}
