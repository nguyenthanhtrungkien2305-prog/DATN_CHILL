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
        if (Schema::hasTable('vouchers')) {
            Schema::table('vouchers', function (Blueprint $table) {
                if (!Schema::hasColumn('vouchers', 'is_points_exchange')) {
                    $table->boolean('is_points_exchange')->default(true)->after('points_required');
                }
                if (!Schema::hasColumn('vouchers', 'assigned_user_id')) {
                    $table->unsignedBigInteger('assigned_user_id')->nullable()->after('is_points_exchange');
                    $table->foreign('assigned_user_id')->references('user_id')->on('users')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('vouchers')) {
            Schema::table('vouchers', function (Blueprint $table) {
                if (Schema::hasColumn('vouchers', 'assigned_user_id')) {
                    $table->dropForeign(['assigned_user_id']);
                    $table->dropColumn('assigned_user_id');
                }
                if (Schema::hasColumn('vouchers', 'is_points_exchange')) {
                    $table->dropColumn('is_points_exchange');
                }
            });
        }
    }
};
