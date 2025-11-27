<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserBlockingLog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        // Générer UUID à la création
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    // Scopes
    public function scopeBlocking($query)
    {
        return $query->where('action', 'block');
    }

    public function scopeUnblocking($query)
    {
        return $query->where('action', 'unblock');
    }

    // Accessors
    public function getActionColorAttribute()
    {
        return match($this->action) {
            'block' => 'red',
            'unblock' => 'green',
            default => 'gray'
        };
    }
}

