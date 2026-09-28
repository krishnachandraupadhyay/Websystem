<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-section, per-component sub-component configuration.
     * 
     * This allows "Card" in Testimonial section to have different sub-components
     * than "Card" in Admin Card section — independently managed.
     */
    public function up(): void
    {
        Schema::create('section_component_subcomponents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')
                  ->constrained('sections')
                  ->onDelete('cascade');
            $table->foreignId('component_id')
                  ->constrained('components')
                  ->onDelete('cascade');
            $table->foreignId('sub_component_id')
                  ->constrained('components')
                  ->onDelete('cascade');
            $table->boolean('status')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();

            // Each (section, component, sub-component) triplet is unique
            $table->unique(
                ['section_id', 'component_id', 'sub_component_id'],
                'sec_comp_subcomp_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_component_subcomponents');
    }
};
