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
        if (Schema::hasTable('vouchers') && !Schema::hasColumn('vouchers', 'points_required')) {
            Schema::table('vouchers', function (Blueprint $table) {
                $table->integer('points_required')->default(10)->after('min_order');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('vouchers') && Schema::hasColumn('vouchers', 'points_required')) {
            Schema::table('vouchers', function (Blueprint $table) {
                $table->dropColumn('points_required');
            });
        }
    }
};
