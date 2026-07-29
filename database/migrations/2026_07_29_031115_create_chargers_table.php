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
        Schema::create('chargers', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->string('inventory_number')->unique();
            $table->enum('condition', ['good', 'damaged', 'under_repair'])->default('good');
            $table->enum('status', ['available', 'borrowed', 'under_repair', 'damaged'])->default('available');
            $table->foreignId('handy_talky_id')->nullable()->constrained('handy_talkies')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chargers');
    }
};
