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
        Schema::table('payment_requests', function (Blueprint $table) {
            // Add new fields
            $table->text('reason_for_payment')->nullable()->after('supplier_id');
            $table->string('responsible_person')->nullable()->after('reason_for_payment');
            $table->string('payment_type')->nullable()->after('responsible_person'); // e.g., 'Advance', 'Final', 'Partial'
            $table->date('received_date')->nullable()->after('payment_type');
            $table->date('submission_date')->nullable()->after('received_date');
            
            // Drop boq_path column
            $table->dropColumn('boq_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            // Reverse: drop new columns
            $table->dropColumn(['reason_for_payment', 'responsible_person', 'payment_type', 'received_date', 'submission_date']);
            
            // Reverse: add back boq_path
            $table->string('boq_path')->nullable();
        });
    }
};
