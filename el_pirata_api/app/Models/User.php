<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = ['id'];

    protected $appends = ['is_complete', 'completeness_percent'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function enigmas()
    {
       return $this->belongsToMany(enigmas::class, 'enigma_user', 'user_id', 'enigma_id')
        ->withPivot([
            'viewed_at',
            'completed_at',
            'unique_code',
            'is_used',
            'attempts',
        ])
        ->withTimestamps();
    }

    /**
     * Tentatives d'énigmes enregistrées dans la table pivot (modèle dédié)
     */
    public function enigmaAttempts()
    {
        return $this->hasMany(enigma_user::class);
    }

    public function settings()
    {
        return $this->hasOne(user_setting::class);
    }

    public function getIsCompleteAttribute(): bool
    {
        return !(
            empty($this->last_name) ||
            empty($this->first_name) ||
            empty($this->email_verified_at) ||
            empty($this->birth_date) ||
            empty($this->address) ||
            empty($this->phone) ||
            empty($this->city) ||
            empty($this->country) ||
            empty($this->sexe)
        );
    }

    public function getCompletenessPercentAttribute(): int
    {
        // Liste des champs considérés comme obligatoires
        $fields = [
            'last_name',
            'first_name',
            'email_verified_at',
            'birth_date',
            'address',
            'phone',
            'city',
            'country',
            'sexe'
        ];

        $completed = 0;

        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $completed++;
            }
        }

        $total = count($fields);

        return intval(($completed / $total) * 100);
    }

    public function transactions()
    {
        return $this->hasMany(transactions::class);
    }

    public function promo_code()
    {
        return $this->hasMany(promo::class);
    }

    public function blockedBy()
    {
        return $this->belongsTo(Admin::class, 'blocked_by');
    }

    public function unblockedBy()
    {
        return $this->belongsTo(Admin::class, 'unblocked_by');
    }

    public function blockingLogs()
    {
        return $this->hasMany(UserBlockingLog::class);
    }

    // Méthodes de blocage
    public function block($adminId, $reason = null)
    {
        $this->update([
            'is_blocked' => true,
            'blocked_at' => now(),
            'blocked_by' => $adminId,
            'block_reason' => $reason,
            'unblocked_at' => null,
            'unblocked_by' => null,
        ]);

        // Enregistrer dans les logs
        UserBlockingLog::create([
            'user_id' => $this->id,
            'admin_id' => $adminId,
            'action' => 'block',
            'reason' => $reason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Révoquer tous les tokens de l'utilisateur
        $this->tokens()->delete();
    }

    public function unblock($adminId, $reason = null)
    {
        $this->update([
            'is_blocked' => false,
            'unblocked_at' => now(),
            'unblocked_by' => $adminId,
        ]);

        // Enregistrer dans les logs
        UserBlockingLog::create([
            'user_id' => $this->id,
            'admin_id' => $adminId,
            'action' => 'unblock',
            'reason' => $reason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    // Accessors
    public function getIsBlockedAttribute()
    {
        return $this->attributes['is_blocked'] ?? false;
    }

    public function getBlockDurationAttribute()
    {
        if (!$this->is_blocked || !$this->blocked_at) {
            return null;
        }

        return $this->blocked_at->diffForHumans();
    }

}
