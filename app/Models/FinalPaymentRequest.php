<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinalPaymentRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_document_id',
        'contract_id',
        'commercial_user_id',
        'project_id',
        'supplier_id',
        'amount',
        'currency',
        'payment_type',
        'notes',
        'status',
        'submitted_to_finance_date',
        'finance_approved_date',
        'payment_date',
        'cheque_number',
        'finance_comments',
    ];

    protected $casts = [
        'submitted_to_finance_date' => 'date',
        'finance_approved_date' => 'date',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the payment document that owns the final payment request.
     */
    public function paymentDocument()
    {
        return $this->belongsTo(PaymentDocument::class);
    }

    /**
     * Get the contract associated with the final payment request.
     */
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    /**
     * Get the commercial user who created the final payment request.
     */
    public function commercialUser()
    {
        return $this->belongsTo(User::class, 'commercial_user_id');
    }

    /**
     * Get the project that owns the final payment request.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the supplier that owns the final payment request.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
