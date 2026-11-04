<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations. The table holds a single row of site-wide settings.
     */
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('about_title', 120);
            $table->text('about_story');
            $table->string('about_photo_path')->nullable();
            $table->string('about_photo_alt', 200)->nullable();
            $table->unsignedSmallInteger('about_photo_width')->nullable();
            $table->unsignedSmallInteger('about_photo_height')->nullable();
            $table->string('about_reasons_title', 120);
            $table->json('about_reasons');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
