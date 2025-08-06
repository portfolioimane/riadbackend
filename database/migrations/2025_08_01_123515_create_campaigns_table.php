<?php
// database/migrations/2025_08_01_000000_create_campaigns_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('active')->default(false);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('campaigns');
    }
};
