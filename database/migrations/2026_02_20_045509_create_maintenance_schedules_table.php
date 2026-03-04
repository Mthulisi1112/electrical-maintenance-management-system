<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'semi_annual', 'annual']);
            $table->string('title');
            $table->text('description');
            $table->json('checklist_items');
            $table->json('required_tools')->nullable();
            $table->integer('estimated_duration_minutes');
            $table->date('start_date');
            $table->date('next_due_date');
            $table->date('last_completed_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index(['asset_id', 'next_due_date', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_schedules');
    }
};