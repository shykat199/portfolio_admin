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
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('hero_prefix', 40)->nullable()->after('whatsapp_number');
            $table->json('hero_words')->nullable()->after('hero_prefix');
            $table->string('hero_headline', 200)->nullable()->after('hero_words');
            $table->string('hero_highlight', 80)->nullable()->after('hero_headline');
            $table->string('hero_supporting', 400)->nullable()->after('hero_highlight');
            $table->string('seo_title', 70)->nullable()->after('hero_supporting');
            $table->string('seo_description', 320)->nullable()->after('seo_title');
            $table->json('seo_keywords')->nullable()->after('seo_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_prefix', 'hero_words', 'hero_headline', 'hero_highlight', 'hero_supporting',
                'seo_title', 'seo_description', 'seo_keywords',
            ]);
        });
    }
};
