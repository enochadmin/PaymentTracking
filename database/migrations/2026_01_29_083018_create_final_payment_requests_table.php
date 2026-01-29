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
        Schema::create('final_payment_requests', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('payment_document_id')->constrained('payment_documents')->onDelete('cascade');
            $table->foreignId('contract_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('commercial_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            
            // Payment details
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('payment_type')->nullable(); // Advance, Final, Partial
            $table->text('notes')->nullable();
            
            // Workflow status
            $table->enum('status', [
                'draft',
                'submitted_to_finance',
                'finance_approved',
                'paid',
                'rejected'
            ])->default('draft');
            
            // Dates
            $table->date('submitted_to_finance_date')->nullable();
            $table->date('finance_approved_date')->nullable();
            $table->date('payment_date')->nullable();
            
            // Finance details
            $table->string('cheque_number')->nullable();
            $table->text('finance_comments')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_payment_requests');
    }
};
