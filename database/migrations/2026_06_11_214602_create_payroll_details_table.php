<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payroll_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->decimal('base_salary', 10, 2)->default(0);
            $table->decimal('total_sale', 10, 2)->default(0);
            $table->decimal('commission', 10, 2)->default(0);
            $table->decimal('total_salary', 10, 2)->default(0);
            
            // Adding other helpful stats
            $table->integer('total_call_time')->default(0);
            $table->integer('days_present')->default(0);
            $table->integer('days_absent')->default(0);
            $table->integer('days_late')->default(0);
            $table->integer('days_leave')->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_details');
    }
};
