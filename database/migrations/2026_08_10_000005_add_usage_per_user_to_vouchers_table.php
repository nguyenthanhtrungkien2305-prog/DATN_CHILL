<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('vouchers')) {
            Schema::table('vouchers', function (Blueprint $table) {
                if (!Schema::hasColumn('vouchers', 'usage_per_user')) {
                    $table->integer('usage_per_user')->default(1)->nullable()->after('usage_limit');
                }
            });

            // Set default 1 per user for existing vouchers
            DB::table('vouchers')->whereNull('usage_per_user')->update(['usage_per_user' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('vouchers')) {
            Schema::table('vouchers', function (Blueprint $table) {
                if (Schema::hasColumn('vouchers', 'usage_per_user')) {
                    $table->dropColumn('usage_per_user');
                }
            });
        }
    }
};
