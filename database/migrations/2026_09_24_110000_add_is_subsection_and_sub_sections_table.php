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
        Schema::table('sections', function (Blueprint $table) {
            $table->boolean('is_subsection')->default(false)->after('description');
        });

        Schema::create('sub_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->string('subsection_name');
            $table->string('subsection_title')->nullable();
            $table->string('subsection_slug')->nullable();
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_sections');

        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn('is_subsection');
        });
    }
};
