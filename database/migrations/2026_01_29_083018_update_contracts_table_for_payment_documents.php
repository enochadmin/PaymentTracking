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
        Schema::table('contracts', function (Blueprint $table) {
            // Drop old foreign key
            $table->dropForeign(['payment_request_id']);
            
            // Rename column
            $table->renameColumn('payment_request_id', 'payment_document_id');
        });
        
        // Add new foreign key constraint
        Schema::table('contracts', function (Blueprint $table) {
            $table->foreign('payment_document_id')->references('id')->on('payment_documents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            // Drop new foreign key
            $table->dropForeign(['payment_document_id']);
            
            // Rename back
            $table->renameColumn('payment_document_id', 'payment_request_id');
        });
        
        // Add back old foreign key
        Schema::table('contracts', function (Blueprint $table) {
            $table->foreign('payment_request_id')->references('id')->on('payment_requests')->onDelete('cascade');
        });
    }
};
