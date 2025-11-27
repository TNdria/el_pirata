<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transactions extends Model
{

    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'stripe_transaction_id',
        'amount_paid',
        'hunt_id',
        'promo_code',
        'paid_at',
        'user_id',
        'payment_type_id',
        'status'
    ];

    protected $dates = [
        'paid_at',
        'created_at',
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();

        // Générer UUID à la création
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment_type()
    {
        return $this->belongsTo(payment_types::class);
    }
    public function hunting()
    {
        return $this->belongsTo(hunting::class, 'hunt_id');
    }

}
