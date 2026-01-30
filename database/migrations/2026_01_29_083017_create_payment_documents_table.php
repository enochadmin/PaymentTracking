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
        Schema::create('payment_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // Procurement Officer
            $table->foreignId('procurement_reviewer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('project_id')->constrained();
            $table->foreignId('supplier_id')->constrained();
            
            // Payment details
            $table->text('reason_for_payment')->nullable();
            $table->string('responsible_person')->nullable();
            $table->string('payment_type')->nullable(); // e.g., 'Advance', 'Final', 'Partial'
            $table->date('received_date')->nullable();
            $table->date('submission_date')->nullable();
            $table->date('reviewed_date')->nullable();
            
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('cost_type'); // e.g., 'Direct', 'Indirect'
            
            // Files
            $table->string('invoice_path')->nullable();
            $table->string('delivery_note_path')->nullable();
            
            // Workflow
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_documents');
    }
};
