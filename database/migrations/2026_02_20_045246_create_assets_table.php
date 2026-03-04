<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique();
            $table->enum('type', ['motor', 'transformer', 'mcc', 'distribution_board', 'vfd', 'switchgear', 'cable', 'other']);
            $table->string('name');
            $table->string('location');
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->decimal('voltage_rating', 10, 2)->nullable();
            $table->decimal('current_rating', 10, 2)->nullable();
            $table->decimal('power_rating', 10, 2)->nullable();
            $table->date('installation_date');
            $table->enum('status', ['operational', 'maintenance', 'faulty', 'decommissioned'])->default('operational');
            $table->text('technical_specs')->nullable();
            $table->string('qr_code')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['type', 'status', 'location']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('assets');
    }
};