<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TreasureValidation extends Model
{
    protected $fillable = [
        'user_id',
        'hunting_id',
        'photo_path',
        'description',
        'status',
        'validated_by',
        'admin_notes',
        'validated_at',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
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
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec la chasse
     */
    public function hunting(): BelongsTo
    {
        return $this->belongsTo(hunting::class);
    }

    /**
     * Relation avec l'admin qui a validé
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'validated_by');
    }

    /**
     * Scope pour les validations en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour les validations approuvées
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope pour les validations rejetées
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Accessor pour l'URL de la photo
     */
    public function getPhotoUrlAttribute()
    {
        return asset('storage/' . $this->photo_path);
    }

    /**
     * Accessor pour la couleur du statut
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Méthode pour approuver la validation
     */
    public function approve($adminId, $adminNotes = null)
    {
        $this->update([
            'status' => 'approved',
            'validated_by' => $adminId,
            'admin_notes' => $adminNotes,
            'validated_at' => now(),
        ]);
    }

    /**
     * Méthode pour rejeter la validation
     */
    public function reject($adminId, $adminNotes = null)
    {
        $this->update([
            'status' => 'rejected',
            'validated_by' => $adminId,
            'admin_notes' => $adminNotes,
            'validated_at' => now(),
        ]);
    }

    /**
     * Vérifier si l'utilisateur peut soumettre une validation pour cette chasse
     */
    public static function canUserSubmitValidation($userId, $huntingId)
    {
        // Vérifier si l'utilisateur a déjà une validation en cours ou approuvée
        return !static::where('user_id', $userId)
            ->where('hunting_id', $huntingId)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();
    }
}
