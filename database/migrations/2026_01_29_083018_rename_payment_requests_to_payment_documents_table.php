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
        // Rename the table
        Schema::rename('payment_requests', 'payment_documents');
        
        // Add new fields and update existing ones
        Schema::table('payment_documents', function (Blueprint $table) {
            // Add procurement reviewer fields
            $table->foreignId('procurement_reviewer_id')->nullable()->after('user_id')->constrained('users')->onDelete('set null');
            $table->date('reviewed_date')->nullable()->after('submission_date');
            
            // Update status enum - remove old values, add new ones
            $table->dropColumn('status');
        });
        
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft')->after('delivery_note_path');
        });
        
        // Remove fields that will move to final_payment_requests
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->dropColumn([
                'contract_link',
                'contract_value',
                'finance_comments',
                'cheque_number',
                'payment_date'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back removed columns
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->string('contract_link')->nullable();
            $table->decimal('contract_value', 15, 2)->nullable();
            $table->text('finance_comments')->nullable();
            $table->string('cheque_number')->nullable();
            $table->date('payment_date')->nullable();
        });
        
        // Remove new columns
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->dropForeign(['procurement_reviewer_id']);
            $table->dropColumn(['procurement_reviewer_id', 'reviewed_date']);
            $table->dropColumn('status');
        });
        
        // Add back old status
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->string('status')->default('draft');
        });
        
        // Rename back to payment_requests
        Schema::rename('payment_documents', 'payment_requests');
    }
};
