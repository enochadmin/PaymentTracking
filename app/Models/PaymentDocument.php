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
        'reviewed_date',
        'amount',
        'currency',
        'cost_type',
        'invoice_path',
        'delivery_note_path',
        'status',
    ];

    protected $casts = [
        'received_date' => 'date',
        'submission_date' => 'date',
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
}
