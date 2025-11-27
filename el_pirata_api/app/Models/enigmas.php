<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class enigmas extends Model
{
    //
    protected $guarded = ['id'];

    /**
     * Utilisateurs ayant interagi avec cette énigme
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'enigma_user', 'enigma_id', 'user_id')
            ->withPivot(['viewed_at', 'completed_at', 'unique_code', 'is_used', 'attempts'])
            ->withTimestamps();
    }

    /**
     * Tentatives d’utilisateurs pour cette énigme
     */
    public function attempts()
    {
        return $this->hasMany(enigma_user::class);
    }

    public function hunting()
    {
        return $this->belongsTo(hunting::class);
    }

}
