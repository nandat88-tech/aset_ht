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
        Schema::table('borrow_transactions', function (Blueprint $table) {
            $table->foreignId('destination_location_id')->nullable()->after('employee_id')->constrained('locations')->nullOnDelete();
        });    
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrow_transactions', function (Blueprint $table) {
           $table->dropForeign(['destination_location_id']);
        $table->dropColumn('destination_location_id');
    });
}
};
