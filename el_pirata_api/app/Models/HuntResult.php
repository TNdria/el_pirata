<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class HuntResult extends Model
{
    protected $fillable = [
        'hunting_id',
        'user_id',
        'rank',
        'completed_enigmas',
        'total_enigmas',
        'completion_percentage',
        'first_enigma_completed_at',
        'last_enigma_completed_at',
        'total_time_seconds',
        'prize_amount',
        'prize_status',
        'prize_awarded_at',
        'awarded_by',
        'notes',
    ];

    protected $casts = [
        'completion_percentage' => 'decimal:2',
        'prize_amount' => 'decimal:2',
        'first_enigma_completed_at' => 'datetime',
        'last_enigma_completed_at' => 'datetime',
        'prize_awarded_at' => 'datetime',
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
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'admin qui a attribué le prix
     */
    public function awardedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'awarded_by');
    }

    /**
     * Scope pour les résultats avec prix
     */
    public function scopeWithPrize($query)
    {
        return $query->whereNotNull('prize_amount')->where('prize_amount', '>', 0);
    }

    /**
     * Scope pour les prix en attente
     */
    public function scopePendingPrize($query)
    {
        return $query->where('prize_status', 'pending');
    }

    /**
     * Scope pour les prix attribués
     */
    public function scopeAwardedPrize($query)
    {
        return $query->where('prize_status', 'awarded');
    }

    /**
     * Scope pour les prix payés
     */
    public function scopePaidPrize($query)
    {
        return $query->where('prize_status', 'paid');
    }

    /**
     * Accessor pour le temps formaté
     */
    public function getTimeFormattedAttribute()
    {
        if (!$this->total_time_seconds) {
            return null;
        }

        $hours = floor($this->total_time_seconds / 3600);
        $minutes = floor(($this->total_time_seconds % 3600) / 60);
        $seconds = $this->total_time_seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Accessor pour la couleur du statut du prix
     */
    public function getPrizeStatusColorAttribute()
    {
        return match ($this->prize_status) {
            'pending' => 'warning',
            'awarded' => 'info',
            'paid' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Accessor pour le texte du statut du prix
     */
    public function getPrizeStatusTextAttribute()
    {
        return match ($this->prize_status) {
            'pending' => 'En attente',
            'awarded' => 'Attribué',
            'paid' => 'Payé',
            default => 'Inconnu',
        };
    }

    /**
     * Méthode pour attribuer un prix
     */
    public function awardPrize($adminId, $prizeAmount, $notes = null)
    {
        $this->update([
            'prize_amount' => $prizeAmount,
            'prize_status' => 'awarded',
            'prize_awarded_at' => now(),
            'awarded_by' => $adminId,
            'notes' => $notes,
        ]);
    }

    /**
     * Méthode pour marquer le prix comme payé
     */
    public function markAsPaid()
    {
        $this->update([
            'prize_status' => 'paid',
        ]);
    }

    /**
     * Vérifier si l'utilisateur est éligible pour un prix
     */
    public function isEligibleForPrize()
    {
        return $this->completed_enigmas === $this->total_enigmas && 
               $this->completion_percentage >= 100;
    }
}
