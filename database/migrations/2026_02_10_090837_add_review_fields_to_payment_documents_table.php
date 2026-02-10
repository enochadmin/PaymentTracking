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
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('status');
            $table->text('review_notes')->nullable()->after('rejection_reason');
            $table->date('review_deadline')->nullable()->after('submission_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_documents', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'review_notes', 'review_deadline']);
        });
    }
};
