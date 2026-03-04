<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('work_order_number')->unique();
            $table->foreignId('asset_id')->constrained();
            $table->foreignId('maintenance_schedule_id')->nullable()->constrained();
            $table->foreignId('technician_id')->constrained('users');
            $table->foreignId('supervisor_id')->constrained('users');
            $table->enum('type', ['preventive', 'corrective', 'emergency', 'inspection']);
            $table->enum('status', ['pending', 'in_progress', 'completed', 'verified', 'cancelled'])->default('pending');
            $table->string('title');
            $table->text('description');
            $table->json('checklist')->nullable();
            $table->json('checklist_responses')->nullable();
            $table->date('scheduled_date');
            $table->date('started_at')->nullable();
            $table->date('completed_date')->nullable();
            $table->date('verified_at')->nullable();
            $table->integer('time_spent_minutes')->nullable();
            $table->json('parts_used')->nullable();
            $table->text('technician_remarks')->nullable();
            $table->text('supervisor_remarks')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'technician_id', 'scheduled_date']);
            $table->index(['asset_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('work_orders');
    }
};