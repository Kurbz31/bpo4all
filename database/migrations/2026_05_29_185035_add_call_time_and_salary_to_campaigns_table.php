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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->decimal('minimum_call_time', 8, 2)->nullable()->after('attendance_method');
            $table->decimal('daily_salary', 10, 2)->nullable()->after('minimum_call_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['minimum_call_time', 'daily_salary']);
        });
    }
};
