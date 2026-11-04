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
            $table->string('linkedin_url')->nullable()->after('favicon_path');
            $table->string('github_url')->nullable()->after('linkedin_url');
            $table->string('facebook_url')->nullable()->after('github_url');
            $table->string('instagram_url')->nullable()->after('facebook_url');
            $table->string('x_url')->nullable()->after('instagram_url');
            $table->string('whatsapp_number', 20)->nullable()->after('x_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['linkedin_url', 'github_url', 'facebook_url', 'instagram_url', 'x_url', 'whatsapp_number']);
        });
    }
};
