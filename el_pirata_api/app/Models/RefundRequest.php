<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RefundRequest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'processed_at' => 'datetime',
        'refund_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        // Générer UUID à la création
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->belongsTo(transactions::class, 'transaction_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'processed_by');
    }

    public function documents()
    {
        return $this->hasMany(RefundDocument::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'orange',
            'approved' => 'blue',
            'rejected' => 'red',
            'processed' => 'green',
            default => 'gray'
        };
    }

    public function getCanBeProcessedAttribute()
    {
        return $this->status === 'approved' && !$this->processed_at;
    }

    // Méthodes
    public function approve($adminId, $adminNotes = null)
    {
        $this->update([
            'status' => 'approved',
            'processed_by' => $adminId,
            'admin_notes' => $adminNotes,
            'processed_at' => now(),
        ]);
    }

    public function reject($adminId, $rejectionReason)
    {
        $this->update([
            'status' => 'rejected',
            'processed_by' => $adminId,
            'rejection_reason' => $rejectionReason,
            'processed_at' => now(),
        ]);
    }

    public function markAsProcessed($refundDate = null)
    {
        $this->update([
            'status' => 'processed',
            'refund_date' => $refundDate ?? now(),
        ]);
    }

    public function isWithinRefundWindow()
    {
        if (!$this->transaction || !$this->transaction->hunting) {
            return false;
        }

        $huntStartDate = $this->transaction->hunting->start_date;
        $refundDeadline = $huntStartDate->subHours(48); // 48h avant la chasse

        return now()->isBefore($refundDeadline);
    }
}

