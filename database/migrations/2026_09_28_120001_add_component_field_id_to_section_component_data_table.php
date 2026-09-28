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
        // 1. First add an index on section_id so the foreign key constraint on section_id is satisfied
        Schema::table('section_component_data', function (Blueprint $table) {
            $table->index('section_id', 'sec_comp_data_section_id_idx');
        });

        // 2. Drop the old unique constraint now that section_id has its own index
        Schema::table('section_component_data', function (Blueprint $table) {
            try {
                $table->dropUnique('sec_comp_subcomp_data_unique');
            } catch (\Throwable $e) {}
        });

        // 3. Add component_field_id and field_name columns and new composite index
        Schema::table('section_component_data', function (Blueprint $table) {
            if (!Schema::hasColumn('section_component_data', 'component_field_id')) {
                $table->foreignId('component_field_id')
                      ->nullable()
                      ->after('sub_component_id')
                      ->constrained('component_fields')
                      ->onDelete('cascade');
            }

            if (!Schema::hasColumn('section_component_data', 'field_name')) {
                $table->string('field_name')->nullable()->after('component_field_id');
            }

            $table->index(['section_id', 'component_id', 'component_field_id'], 'sec_comp_field_data_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('section_component_data', function (Blueprint $table) {
            try {
                $table->dropIndex('sec_comp_field_data_idx');
            } catch (\Throwable $e) {}

            if (Schema::hasColumn('section_component_data', 'component_field_id')) {
                $table->dropForeign(['component_field_id']);
                $table->dropColumn('component_field_id');
            }

            if (Schema::hasColumn('section_component_data', 'field_name')) {
                $table->dropColumn('field_name');
            }

            try {
                $table->unique(
                    ['section_id', 'component_id', 'sub_component_id'],
                    'sec_comp_subcomp_data_unique'
                );
            } catch (\Throwable $e) {}

            try {
                $table->dropIndex('sec_comp_data_section_id_idx');
            } catch (\Throwable $e) {}
        });
    }
};
