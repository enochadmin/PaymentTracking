<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 'contact_person', 'email', 'phone', 'address', 'supplier_type',
    ];

    /**
     * Get the contracts for the supplier.
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get the payment documents for the supplier.
     */
    public function paymentDocuments()
    {
        return $this->hasMany(PaymentDocument::class);
    }

    /**
     * Get the final payment requests for the supplier.
     */
    public function finalPaymentRequests()
    {
        return $this->hasMany(FinalPaymentRequest::class);
    }
}
