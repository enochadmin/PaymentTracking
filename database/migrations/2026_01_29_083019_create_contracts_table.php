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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_document_id')->constrained('payment_documents')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Commercial user who created
            
            $table->string('contract_number')->unique();
            $table->string('contract_title');
            $table->enum('contract_type', ['Subcontractor', 'Consultant']);
            
            $table->date('start_date');
            $table->date('end_date');
            
            $table->decimal('contract_value', 15, 2);
            $table->string('currency', 3)->default('USD');
            
            $table->text('payment_terms')->nullable();
            $table->text('scope_of_work')->nullable();
            $table->text('deliverables')->nullable();
            
            $table->string('contract_document_path')->nullable();
            
            $table->enum('status', ['draft', 'active', 'completed', 'terminated'])->default('draft');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
