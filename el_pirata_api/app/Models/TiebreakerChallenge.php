<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TiebreakerChallenge extends Model
{
    protected $fillable = [
        'hunting_id',
        'title',
        'description',
        'question',
        'correct_answer',
        'hints',
        'time_limit_minutes',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Relation avec la chasse
     */
    public function hunting(): BelongsTo
    {
        return $this->belongsTo(hunting::class);
    }

    /**
     * Relation avec les participations
     */
    public function participations(): HasMany
    {
        return $this->hasMany(TiebreakerParticipation::class);
    }

    /**
     * Scope pour les défis actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('starts_at', '<=', now())
                    ->where('ends_at', '>=', now());
    }

    /**
     * Scope pour les défis d'une chasse
     */
    public function scopeForHunting($query, $huntingId)
    {
        return $query->where('hunting_id', $huntingId);
    }

    /**
     * Vérifier si le défi est en cours
     */
    public function isActive()
    {
        return $this->is_active && 
               $this->starts_at <= now() && 
               $this->ends_at >= now();
    }

    /**
     * Vérifier si le défi est terminé
     */
    public function isFinished()
    {
        return $this->ends_at < now();
    }

    /**
     * Obtenir le temps restant en secondes
     */
    public function getTimeRemainingSeconds()
    {
        if (!$this->isActive()) {
            return 0;
        }

        return max(0, $this->ends_at->diffInSeconds(now()));
    }

    /**
     * Obtenir le temps restant formaté
     */
    public function getTimeRemainingFormatted()
    {
        $seconds = $this->getTimeRemainingSeconds();
        
        if ($seconds <= 0) {
            return 'Terminé';
        }

        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        return sprintf('%02d:%02d', $minutes, $remainingSeconds);
    }

    /**
     * Obtenir le classement des participants
     */
    public function getLeaderboard()
    {
        return $this->participations()
            ->whereNotNull('answered_at')
            ->orderBy('is_correct', 'desc')
            ->orderBy('response_time_seconds', 'asc')
            ->orderBy('answered_at', 'asc')
            ->get()
            ->map(function ($participation, $index) {
                $participation->rank = $index + 1;
                return $participation;
            });
    }

    /**
     * Vérifier si un utilisateur peut participer
     */
    public function canUserParticipate($userId)
    {
        if (!$this->isActive()) {
            return false;
        }

        return !$this->participations()
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Accessor pour le statut
     */
    public function getStatusAttribute()
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->starts_at > now()) {
            return 'scheduled';
        }

        if ($this->ends_at < now()) {
            return 'finished';
        }

        return 'active';
    }

    /**
     * Accessor pour la couleur du statut
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'active' => 'success',
            'scheduled' => 'info',
            'finished' => 'warning',
            'inactive' => 'danger',
            default => 'secondary',
        };
    }
}
