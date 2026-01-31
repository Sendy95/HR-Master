<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_no')->constrained('employees')->onDelete('cascade');
            $table->string('field_name'); 
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->foreignId('changed_by')->constrained('users');
            $table->string('reason')->nullable(); // e.g., "Self-Service Update"
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('employee_logs'); }
};