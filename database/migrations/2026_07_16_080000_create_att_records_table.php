use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('att_records', function (Blueprint $table) {
            $table->id('att_id');
            $table->string('att_number', 50)->nullable(); // e.g., 2026-001
            $table->foreignId('employee_id')->constrained('employees', 'employee_id')->cascadeOnDelete();
            
            $table->string('destination', 255);
            $table->text('purpose');
            
            // Travel Schedule Details
            $table->date('departure_date');
            $table->time('departure_time')->nullable();
            $table->date('arrival_date');
            $table->time('arrival_time')->nullable();
            
            // Approval Classification
            $table->enum('travel_type', ['Official Business', 'Official Time'])->default('Official Business');
            
            // Signatories & Tracking
            $table->string('supervisor_name', 255)->nullable();
            $table->string('campus_director', 255)->default('LIZA L. QUIMSON, EdD');
            $table->foreignId('approved_by')->nullable()->constrained('admin_users', 'admin_id')->nullOnDelete();
            
            // Document Scanned Upload
            $table->string('image_url', 255)->nullable();
            
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->date('filed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('att_records');
    }
};