<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Thêm cột address vào sau cột email, cho phép rỗng (nullable)
            $table->text('address')->nullable()->after('email');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Lệnh xóa cột nếu bạn muốn rollback (quay xe)
            $table->dropColumn('address');
        });
    }
};
