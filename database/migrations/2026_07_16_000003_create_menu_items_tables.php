<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_description', 500)->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->decimal('discounted_price', 8, 2)->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->unsignedSmallInteger('prep_time_minutes')->nullable();
            $table->integer('stock_quantity')->nullable(); // null = not tracked
            $table->unsignedInteger('low_stock_threshold')->default(5);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_sold_out')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_bestseller')->default(false);
            $table->boolean('needs_verification')->default(false); // price/details pending client review
            $table->time('available_from')->nullable();
            $table->time('available_until')->nullable();
            $table->json('available_days')->nullable(); // [0..6], null = every day
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('seo_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->timestamps();

            $table->index(['category_id', 'is_available', 'sort_order']);
            $table->index('is_featured');
            $table->index('is_bestseller');
        });

        Schema::create('menu_item_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. "Sandwich", "Per Lb", "On a Sub Roll"
            $table->decimal('price', 8, 2);
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('menu_item_modifier_group', function (Blueprint $table) {
            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('modifier_group_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->primary(['menu_item_id', 'modifier_group_id']);
        });

        Schema::create('dietary_label_menu_item', function (Blueprint $table) {
            $table->foreignId('dietary_label_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $table->primary(['dietary_label_id', 'menu_item_id']);
        });

        Schema::create('allergen_menu_item', function (Blueprint $table) {
            $table->foreignId('allergen_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $table->primary(['allergen_id', 'menu_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('allergen_menu_item');
        Schema::dropIfExists('dietary_label_menu_item');
        Schema::dropIfExists('menu_item_modifier_group');
        Schema::dropIfExists('menu_item_variations');
        Schema::dropIfExists('menu_items');
    }
};
