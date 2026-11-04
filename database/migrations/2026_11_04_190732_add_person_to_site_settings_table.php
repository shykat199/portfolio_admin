<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations. These describe you to search engines (Person schema).
     */
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('person_name', 100)->nullable()->after('seo_keywords');
            $table->string('person_job_title', 120)->nullable()->after('person_name');
            $table->string('person_description', 300)->nullable()->after('person_job_title');
            $table->string('person_email', 200)->nullable()->after('person_description');
            $table->string('person_phone', 30)->nullable()->after('person_email');
            $table->string('person_city', 80)->nullable()->after('person_phone');
            $table->string('person_country', 80)->nullable()->after('person_city');
            $table->json('person_skills')->nullable()->after('person_country');
            $table->json('person_languages')->nullable()->after('person_skills');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'person_name', 'person_job_title', 'person_description', 'person_email', 'person_phone',
                'person_city', 'person_country', 'person_skills', 'person_languages',
            ]);
        });
    }
};
