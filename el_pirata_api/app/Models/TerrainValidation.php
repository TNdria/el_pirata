<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TerrainValidation extends Model
{
    protected $fillable = [
        'user_id',
        'terrain_code_id',
        'user_latitude',
        'user_longitude',
        'distance_meters',
        'is_valid',
        'validated_at',
    ];

    protected $casts = [
        'user_latitude' => 'decimal:8',
        'user_longitude' => 'decimal:8',
        'is_valid' => 'boolean',
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
     * Relation avec le code de validation terrain
     */
    public function terrainCode(): BelongsTo
    {
        return $this->belongsTo(TerrainValidationCode::class, 'terrain_code_id');
    }

    /**
     * Scope pour les validations réussies
     */
    public function scopeValid($query)
    {
        return $query->where('is_valid', true);
    }

    /**
     * Scope pour les validations échouées
     */
    public function scopeInvalid($query)
    {
        return $query->where('is_valid', false);
    }

    /**
     * Accessor pour la couleur du statut
     */
    public function getStatusColorAttribute()
    {
        return $this->is_valid ? 'success' : 'danger';
    }

    /**
     * Accessor pour le texte du statut
     */
    public function getStatusTextAttribute()
    {
        return $this->is_valid ? 'Validé' : 'Échec';
    }

    /**
     * Accessor pour la distance formatée
     */
    public function getDistanceFormattedAttribute()
    {
        if ($this->distance_meters < 1000) {
            return $this->distance_meters . ' m';
        }
        
        return round($this->distance_meters / 1000, 2) . ' km';
    }
}
