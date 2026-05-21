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
        Schema::create('posts', function (Blueprint $table) {
    $table->id('post_id');
    $table->string('title');
    $table->string('slug')->unique();
    $table->longText('content')->nullable();
    $table->string('thumbnail')->nullable();
    $table->boolean('status')->default(true);
    $table->unsignedBigInteger('categories_post_id');
    $table->unsignedBigInteger('auth_id'); // Mapping sang users
    $table->text('images')->nullable();
    $table->timestamps();

    $table->foreign('categories_post_id')->references('categories_post_id')->on('categories_post')->onDelete('cascade');
    $table->foreign('auth_id')->references('user_id')->on('users')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
