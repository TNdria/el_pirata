<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class enigma_user extends Model
{
    protected $table = 'enigma_user';
    protected $guarded = ['id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function enigma()
    {
        return $this->belongsTo(enigmas::class);
    }

}
