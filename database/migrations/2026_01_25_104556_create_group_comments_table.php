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
        Schema::create('group_comments', function (Blueprint $table) {
            $table->id();
            $table->string('context');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('books')->cascadeOnDelete();
            $table->string('pdf_read')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_comments');
    }
};
