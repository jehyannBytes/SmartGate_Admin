<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('leave_requests');

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id('leave_id');
            $table->foreignId('employee_id')->constrained('employees', 'employee_id')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('admin_users', 'admin_id')->nullOnDelete();
            
            $table->string('leave_type', 100);
            $table->string('details_location', 255)->nullable(); // CS Form 6 Sec 6.B (e.g., Within Philippines, Abroad, In Hospital)
            $table->string('details_specify', 255)->nullable();  // Specific illness, degree program, etc.
            
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_days', 4, 1); // Supports 0.5, 1, 1.5 days
            $table->string('commutation', 50)->default('Not Requested'); // CS Form 6 Sec 6.D
            
            $table->string('image_url', 255)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->timestamp('filed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};