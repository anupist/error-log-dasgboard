<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'api_url',
        'api_token',
        'environment',
        'status',
        'notes',
    ];

    protected $casts = [
        'api_token' => 'encrypted',
    ];

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getEnvironmentBadgeColorAttribute(): string
    {
        return match ($this->environment) {
            'production' => 'red',
            'staging'    => 'yellow',
            'development'=> 'green',
            default      => 'gray',
        };
    }

    // ─── Mutators ────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->name);
            }
        });
    }

    // ─── Route model binding ─────────────────────────────────────────────────

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
