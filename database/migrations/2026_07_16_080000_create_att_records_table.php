<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('att_records', function (Blueprint $table) {
            $table->id('att_id');
            $table->foreignId('employee_id')->constrained('employees', 'employee_id')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('admin_users', 'admin_id')->nullOnDelete();
            $table->string('image_url', 255)->nullable();
            $table->date('att_date');
            $table->string('reason', 255)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('filed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('att_records');
    }
};
