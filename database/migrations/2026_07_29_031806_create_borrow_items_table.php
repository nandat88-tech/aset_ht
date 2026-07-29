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
        Schema::create('borrow_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrow_transaction_id')->constrained('borrow_transactions')->cascadeOnDelete();
            $table->foreignId('handy_talky_id')->nullable()->constrained('handy_talkies')->nullOnDelete();
            $table->foreignId('charger_id')->nullable()->constrained('chargers')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrow_items');
    }
};
