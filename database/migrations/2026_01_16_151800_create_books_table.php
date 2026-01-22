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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('authors')->cascadeOnDelete();
            $table->string('title');
            $table->text('dec');
            $table->year('publish_year');
            $table->string('pdf_read')->nullable();
            $table->string('pdf_download')->nullable();
            $table->string('audio')->nullable();
            $table->decimal('rating', 2, 1)->default(1);
            $table->string('language');
            $table->string('status_case');
            $table->boolean('is_readable')->default(false);
            $table->boolean('is_downloadable')->default(false);
            $table->boolean('has_audio')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
