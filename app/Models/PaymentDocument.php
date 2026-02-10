<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'project_id',
        'supplier_id',
        'procurement_reviewer_id',
        'reason_for_payment',
        'responsible_person',
        'payment_type',
        'received_date',
        'submission_date',
        'review_deadline',
        'reviewed_date',
        'amount',
        'currency',
        'cost_type',
        'invoice_path',
        'delivery_note_path',
        'status',
        'rejection_reason',
        'review_notes',
    ];

    protected $casts = [
        'received_date' => 'date',
        'submission_date' => 'date',
        'review_deadline' => 'date',
        'reviewed_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the procurement officer who created the document.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the procurement reviewer who reviewed the document.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'procurement_reviewer_id');
    }

    /**
     * Get the project that owns the payment document.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the supplier that owns the payment document.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the contract associated with the payment document.
     */
    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    /**
     * Get the final payment requests for the payment document.
     */
    public function finalPaymentRequests()
    {
        return $this->hasMany(FinalPaymentRequest::class);
    }

    /**
     * Get the deadline status attribute.
     * Returns: 'expired', 'near', or 'on_track'
     */
    public function getDeadlineStatusAttribute()
    {
        if (!$this->review_deadline) {
            return null;
        }

        $today = now()->startOfDay();
        $deadline = $this->review_deadline->startOfDay();
        $daysUntilDeadline = $today->diffInDays($deadline, false);

        if ($daysUntilDeadline < 0) {
            return 'expired';
        } elseif ($daysUntilDeadline <= 3) {
            return 'near';
        } else {
            return 'on_track';
        }
    }

    /**
     * Scope for documents with expired deadlines.
     */
    public function scopeExpiredDeadline($query)
    {
        return $query->whereNotNull('review_deadline')
            ->where('review_deadline', '<', now()->startOfDay());
    }

    /**
     * Scope for documents with near deadlines (within 3 days).
     */
    public function scopeNearDeadline($query)
    {
        return $query->whereNotNull('review_deadline')
            ->where('review_deadline', '>=', now()->startOfDay())
            ->where('review_deadline', '<=', now()->addDays(3)->endOfDay());
    }

    /**
     * Scope for documents on track (deadline > 3 days away).
     */
    public function scopeOnTrackDeadline($query)
    {
        return $query->whereNotNull('review_deadline')
            ->where('review_deadline', '>', now()->addDays(3)->endOfDay());
    }
}
