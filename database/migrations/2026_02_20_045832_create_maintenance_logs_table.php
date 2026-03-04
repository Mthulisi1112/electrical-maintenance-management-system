<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained();
            $table->foreignId('asset_id')->constrained();
            $table->foreignId('performed_by')->constrained('users');
            $table->enum('maintenance_type', ['preventive', 'corrective', 'inspection', 'calibration', 'repair']);
            $table->text('actions_taken');
            $table->json('measurements')->nullable();
            $table->json('parts_used')->nullable();
            $table->integer('time_spent_minutes');
            $table->text('observations')->nullable();
            $table->json('attachments')->nullable();
            $table->enum('result', ['successful', 'partial', 'failed', 'deferred'])->default('successful');
            $table->date('next_maintenance_date')->nullable();
            $table->timestamps();
            
            $table->index(['asset_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_logs');
    }
};