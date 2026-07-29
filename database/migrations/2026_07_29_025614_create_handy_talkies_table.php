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
        Schema::create('handy_talkies', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->string('inventory_number')->unique();
            $table->string('brand');
            $table->string('model');
            $table->string('frequency')->nullable();
            $table->string('photo_url')->nullable();
            $table->enum('condition', ['good', 'damaged', 'under_repair'])->default('good');
            $table->enum('status', ['available', 'borrowed', 'under_repair', 'damaged'])->default('available');
            $table->date('purchase_date')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('handy_talkies');
    }
};
