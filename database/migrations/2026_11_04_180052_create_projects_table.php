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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 160);
            $table->string('slug')->unique();
            $table->boolean('is_demo')->default(false);
            $table->string('category', 60);
            $table->string('year', 4);
            $table->string('service')->nullable();
            $table->string('problem', 300);
            $table->string('solution', 300);
            $table->string('result', 300);
            $table->text('overview');
            $table->text('client_problem');
            $table->json('built');
            $table->json('tech');
            $table->json('results');
            $table->string('cover_path');
            $table->string('cover_alt');
            $table->json('screenshots');
            $table->string('seo_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->json('meta_keywords')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
