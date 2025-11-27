<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payment_types extends Model
{
    //
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'uuid';
    protected $fillable = ['code', 'label_fr', 'direction', 'is_archived'];

    public function transactions()
    {
        return $this->hasMany(transactions::class);
    }
}
