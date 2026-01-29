<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // Requester
            $table->foreignId('project_id')->constrained();
            $table->foreignId('supplier_id')->constrained();
            
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('cost_type'); // e.g., 'Direct', 'Indirect'
            
            // Files
            $table->string('invoice_path')->nullable();
            $table->string('boq_path')->nullable(); // Bill of Quantities
            $table->string('delivery_note_path')->nullable();
            
            // Workflow
            $table->string('status')->default('draft'); // draft, submitted, commercial_approved, finance_approved, paid, rejected
            
            // Commercial
            $table->string('contract_link')->nullable(); // Or link to a Contract model if exists
            $table->decimal('contract_value', 15, 2)->nullable(); // Snapshot or reference
            
            // Finance
            $table->text('finance_comments')->nullable();
            $table->string('cheque_number')->nullable();
            $table->date('payment_date')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_requests');
    }
};
