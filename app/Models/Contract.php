<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_document_id',
        'supplier_id',
        'project_id',
        'user_id',
        'contract_number',
        'contract_title',
        'contract_type',
        'start_date',
        'end_date',
        'contract_value',
        'currency',
        'payment_terms',
        'scope_of_work',
        'deliverables',
        'contract_document_path',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'contract_value' => 'decimal:2',
    ];

    /**
     * Get the payment document that owns the contract.
     */
    public function paymentDocument()
    {
        return $this->belongsTo(PaymentDocument::class);
    }

    /**
     * Get the supplier that owns the contract.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the project that owns the contract.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user (commercial) who created the contract.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the final payment requests for the contract.
     */
    public function finalPaymentRequests()
    {
        return $this->hasMany(FinalPaymentRequest::class);
    }
}
