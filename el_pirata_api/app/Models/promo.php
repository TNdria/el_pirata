<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class promo extends Model
{
    //
    protected $guarded = ['id'];

    protected $casts = [
        'valid_until' => 'datetime',
        'used_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hunting()
    {
        return $this->belongsTo(hunting::class, 'hunt_id');
    }

    public function transactions()
    {
        return $this->hasMany(transactions::class, 'promo_code', 'code');
    }

    // Scopes
    public function scopeVipCodes($query)
    {
        return $query->where('type', 'vip_code');
    }

    public function scopeValid($query)
    {
        return $query->where('is_used', false)
            ->where(function ($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>', now());
            });
    }

    // Accessors
    public function getIsExpiredAttribute()
    {
        return $this->valid_until && $this->valid_until->isPast();
    }

    public function getIsValidAttribute()
    {
        return !$this->is_used && !$this->is_expired;
    }
}
