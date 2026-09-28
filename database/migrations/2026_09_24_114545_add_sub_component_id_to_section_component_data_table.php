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
        Schema::table('section_component_data', function (Blueprint $table) {
            if (!Schema::hasColumn('section_component_data', 'sub_component_id')) {
                $table->foreignId('sub_component_id')->nullable()->after('component_id')->constrained('components')->onDelete('cascade');
            }
            try {
                $table->dropUnique(['section_id', 'component_id']);
            } catch (\Throwable $e) {}

            try {
                $table->unique(['section_id', 'component_id', 'sub_component_id'], 'sec_comp_subcomp_data_unique');
            } catch (\Throwable $e) {}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('section_component_data', function (Blueprint $table) {
            if (Schema::hasColumn('section_component_data', 'sub_component_id')) {
                $table->dropForeign(['sub_component_id']);
                $table->dropColumn('sub_component_id');
            }
        });
    }
};
