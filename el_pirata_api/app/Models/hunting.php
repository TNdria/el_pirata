<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class hunting extends Model
{
    //
    protected $guarded = ['id'];
    protected $appends = ['purchase_count'];

    protected $casts = [
        'start_date' => 'datetime',
    ];

    public function transactions()
    {
        return $this->hasMany(transactions::class, 'hunt_id');
    }

    public function getPurchaseCountAttribute()
    {
        return $this->transactions()
            ->whereHas('payment_type', fn($q) => $q->where('code', 'ticket_purchase'))
            ->where('status', 'validated')
            ->count();
    }

    public function enigmas()
    {
        return $this->hasMany(enigmas::class, 'hunting_id');
    }
}
