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
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('role', 120);
            $table->string('company', 120);
            $table->string('company_url')->nullable();
            $table->string('type', 40);
            $table->string('location', 120)->nullable();
            $table->string('start', 40);
            $table->string('end', 40)->nullable();
            $table->text('summary');
            $table->json('highlights');
            $table->json('tech');
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
        Schema::dropIfExists('experiences');
    }
};
