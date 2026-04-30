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
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->unsignedBigInteger('employee_id'); // Pastikan tipe data sama dengan id di tabel employees
            $table->string('document_type', 50);
            $table->text('file_path');
            $table->integer('upload_count')->default(0);
            $table->timestamps(); // Menghasilkan created_at dan updated_at

            // Definisi Foreign Key
            $table->foreign('employee_id')
                  ->references('id')
                  ->on('employees')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
    }
};