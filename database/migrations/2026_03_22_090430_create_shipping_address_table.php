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
       Schema::create('shipping_address', function (Blueprint $table) {
    $table->id('address_id');
    $table->unsignedBigInteger('user_id');
    $table->string('full_name');
    $table->string('phone');
    $table->text('address_line');
    $table->boolean('is_default')->default(false);
    $table->timestamps();

    $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_address');
    }
};
