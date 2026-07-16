<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle', 500)->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('button2_text')->nullable();
            $table->string('button2_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('banner'); // banner | offer
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('badge_text')->nullable(); // e.g. "SAVE $4"
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });

        Schema::create('catering_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('price_label')->nullable(); // "per person", "starting at"
            $table->string('serves')->nullable();      // "10-12 people"
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->boolean('needs_verification')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question', 500);
            $table->text('answer');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('image');
            $table->string('image_alt')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable(); // sanitized HTML
            $table->boolean('is_active')->default(true);
            $table->string('seo_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('navigation_links', function (Blueprint $table) {
            $table->id();
            $table->string('location')->default('header'); // header | footer
            $table->string('label');
            $table->string('url');
            $table->boolean('new_tab')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['location', 'is_active', 'sort_order']);
        });

        Schema::create('business_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('day_of_week')->unique(); // 0=Sunday .. 6=Saturday
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
        });

        Schema::create('holiday_hours', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('label');
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->boolean('is_closed')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holiday_hours');
        Schema::dropIfExists('business_hours');
        Schema::dropIfExists('navigation_links');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('gallery_images');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('catering_packages');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('hero_slides');
    }
};
