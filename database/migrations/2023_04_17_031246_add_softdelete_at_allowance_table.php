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
        Schema::table('allowance', function (Blueprint $table) {
            $table->string('status')->nullable()->default('active');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('allowance', function (Blueprint $table) {
            $table->dropColumn('deduction');
            $table->dropSoftDeletes();
        });
    }
};
