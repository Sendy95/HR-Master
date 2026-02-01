<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_update_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('employee_no', 10);
            $table->unsignedBigInteger('update_id')->nullable()
                ->comment('FK to employee_updates.id');

            $table->string('field_name', 100);
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();

            $table->enum('action', ['Submit', 'Approve', 'Reject']);
            $table->enum('approval_status', ['Pending', 'Approved', 'Rejected'])
                  ->default('Pending');

            $table->string('acted_by', 10)->nullable()
                  ->comment('employee_no of actor');
            $table->enum('acted_role', [
                'employee',
                'hr_staff',
                'hr_manager',
                'hr_admin',
                'super_admin'
            ])->nullable();

            $table->string('ip_address', 45)->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('employee_no');
            $table->index('update_id');
            $table->index('action');
            $table->index('approval_status');

            // Optional FK (aktifkan jika mau strict)
            // $table->foreign('update_id')
            //       ->references('id')
            //       ->on('employee_updates')
            //       ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_update_logs');
    }
};