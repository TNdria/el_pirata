<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TiebreakerParticipation extends Model
{
    protected $fillable = [
        'tiebreaker_challenge_id',
        'user_id',
        'answer',
        'is_correct',
        'answered_at',
        'response_time_seconds',
        'rank',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'answered_at' => 'datetime',
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
     * Relation avec le défi
     */
    public function tiebreakerChallenge(): BelongsTo
    {
        return $this->belongsTo(TiebreakerChallenge::class, 'tiebreaker_challenge_id');
    }

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour les participations correctes
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    /**
     * Scope pour les participations incorrectes
     */
    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }

    /**
     * Scope pour les participations avec réponse
     */
    public function scopeAnswered($query)
    {
        return $query->whereNotNull('answered_at');
    }

    /**
     * Accessor pour le temps de réponse formaté
     */
    public function getResponseTimeFormattedAttribute()
    {
        if (!$this->response_time_seconds) {
            return null;
        }

        $minutes = floor($this->response_time_seconds / 60);
        $seconds = $this->response_time_seconds % 60;

        if ($minutes > 0) {
            return sprintf('%02d:%02d', $minutes, $seconds);
        }

        return sprintf('%02ds', $seconds);
    }

    /**
     * Accessor pour la couleur du statut
     */
    public function getStatusColorAttribute()
    {
        if ($this->is_correct === null) {
            return 'secondary';
        }

        return $this->is_correct ? 'success' : 'danger';
    }

    /**
     * Accessor pour le texte du statut
     */
    public function getStatusTextAttribute()
    {
        if ($this->is_correct === null) {
            return 'En attente';
        }

        return $this->is_correct ? 'Correct' : 'Incorrect';
    }
}
