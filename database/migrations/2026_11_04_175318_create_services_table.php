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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('slug')->unique();
            $table->string('for_who', 300);
            $table->unsignedInteger('price');
            $table->string('price_suffix', 20)->nullable();
            $table->boolean('billed_monthly')->default(false);
            $table->string('time_label', 40);
            $table->string('time', 80);
            $table->json('includes');
            $table->string('heading', 160);
            $table->string('intro', 320);
            $table->text('story');
            $table->json('ideal_for');
            $table->json('faq');
            $table->json('related_projects');
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
        Schema::dropIfExists('services');
    }
};
