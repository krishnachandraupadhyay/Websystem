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
        Schema::dropIfExists('component_subcomponents');
        Schema::create('component_subcomponents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_component_id')->constrained('components')->cascadeOnDelete();
            $table->foreignId('sub_component_id')->constrained('components')->cascadeOnDelete();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->unique(['parent_component_id', 'sub_component_id'], 'comp_subcomp_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('component_subcomponents');
    }
};
