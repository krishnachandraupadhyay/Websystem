<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_section_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_section_id')->constrained('sub_sections')->onDelete('cascade');
            $table->foreignId('component_id')->constrained('components')->onDelete('cascade');
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->unique(['sub_section_id', 'component_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_section_components');
    }
};
