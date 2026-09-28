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
        Schema::create('component_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('component_id')->constrained('components')->onDelete('cascade');
            $table->string('field_name');
            $table->string('field_label');
            $table->string('field_type', 50)->default('text'); // text, textarea, url, image, file, video, select, checkbox, number
            $table->string('placeholder')->nullable();
            $table->text('default_value')->nullable();
            $table->string('help_text')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('options')->nullable(); // For select dropdown options: [{"value": "val", "label": "Label"}]
            $table->text('validation_rules')->nullable();
            $table->timestamps();

            $table->index(['component_id', 'sort_order']);
            $table->index(['component_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('component_fields');
    }
};
