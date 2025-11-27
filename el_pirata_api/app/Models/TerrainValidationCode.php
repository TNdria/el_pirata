<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TerrainValidationCode extends Model
{
    protected $fillable = [
        'hunting_id',
        'code',
        'location_name',
        'latitude',
        'longitude',
        'radius_meters',
        'description',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
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
     * Relation avec les validations
     */
    public function validations(): HasMany
    {
        return $this->hasMany(TerrainValidation::class, 'terrain_code_id');
    }

    /**
     * Scope pour les codes actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope pour les codes d'une chasse
     */
    public function scopeForHunting($query, $huntingId)
    {
        return $query->where('hunting_id', $huntingId);
    }

    /**
     * Méthode pour calculer la distance entre deux points GPS
     */
    public function calculateDistance($userLatitude, $userLongitude)
    {
        $earthRadius = 6371000; // Rayon de la Terre en mètres

        $lat1 = deg2rad($this->latitude);
        $lon1 = deg2rad($this->longitude);
        $lat2 = deg2rad($userLatitude);
        $lon2 = deg2rad($userLongitude);

        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1) * cos($lat2) *
             sin($deltaLon / 2) * sin($deltaLon / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance en mètres
    }

    /**
     * Vérifier si l'utilisateur est dans le rayon de validation
     */
    public function isUserInRadius($userLatitude, $userLongitude)
    {
        $distance = $this->calculateDistance($userLatitude, $userLongitude);
        return $distance <= $this->radius_meters;
    }

    /**
     * Générer un code unique pour une chasse
     */
    public static function generateUniqueCode($huntingId)
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    /**
     * Accessor pour le statut
     */
    public function getStatusAttribute()
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'expired';
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
            'expired' => 'warning',
            'inactive' => 'danger',
            default => 'secondary',
        };
    }
}
