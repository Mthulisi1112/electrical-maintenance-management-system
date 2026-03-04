<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('faults', function (Blueprint $table) {
            $table->id();
            $table->string('fault_number')->unique();
            $table->foreignId('asset_id')->constrained();
            $table->foreignId('reported_by')->constrained('users');
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->enum('fault_type', ['trip', 'overload', 'short_circuit', 'earth_fault', 'overheating', 'mechanical', 'other']);
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['reported', 'investigating', 'in_progress', 'resolved', 'closed'])->default('reported');
            $table->text('description');
            $table->json('symptoms')->nullable();
            $table->json('images')->nullable();
            $table->dateTime('downtime_start');
            $table->dateTime('downtime_end')->nullable();
            $table->integer('downtime_minutes')->nullable();
            $table->text('root_cause')->nullable();
            $table->text('corrective_actions')->nullable();
            $table->json('parts_replaced')->nullable();
            $table->boolean('requires_followup')->default(false);
            $table->timestamps();
            
            $table->index(['asset_id', 'status', 'severity']);
            $table->index(['reported_by', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('faults');
    }
};