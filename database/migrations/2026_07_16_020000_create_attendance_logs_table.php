<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->foreignId('employee_id')->constrained('employees', 'employee_id')->cascadeOnDelete();
            $table->foreignId('device_id')->nullable()->constrained('devices', 'device_id')->nullOnDelete();
            $table->enum('log_type', ['Morning In', 'Morning Out', 'Afternoon In', 'Afternoon Out']);
            $table->timestamp('scanned_at');
            $table->boolean('geofence_passed')->default(false);
            $table->boolean('face_verified')->default(false);
            $table->enum('sync_status', ['pending', 'synced', 'failed'])->default('pending');
            $table->string('local_uuid')->nullable()->unique();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
