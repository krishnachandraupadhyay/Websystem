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
        // 1. components table
        Schema::table('components', function (Blueprint $table) {
            if (!Schema::hasColumn('components', 'is_multiple')) {
                $table->boolean('is_multiple')->default(false)->after('is_subcomponent');
            }
        });

        // 2. section_components pivot table
        Schema::table('section_components', function (Blueprint $table) {
            if (!Schema::hasColumn('section_components', 'is_multiple')) {
                $table->boolean('is_multiple')->default(false)->after('status');
            }
        });

        // 3. section_component_subcomponents pivot table
        Schema::table('section_component_subcomponents', function (Blueprint $table) {
            if (!Schema::hasColumn('section_component_subcomponents', 'is_multiple')) {
                $table->boolean('is_multiple')->default(false)->after('status');
            }
        });

        // 4. section_component_data table
        Schema::table('section_component_data', function (Blueprint $table) {
            if (!Schema::hasColumn('section_component_data', 'instance_index')) {
                $table->unsignedInteger('instance_index')->default(0)->after('component_field_id');
                $table->index(['section_id', 'component_id', 'instance_index'], 'sec_comp_inst_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('section_component_data', function (Blueprint $table) {
            if (Schema::hasColumn('section_component_data', 'instance_index')) {
                try {
                    $table->dropIndex('sec_comp_inst_idx');
                } catch (\Throwable $e) {}
                $table->dropColumn('instance_index');
            }
        });

        Schema::table('section_component_subcomponents', function (Blueprint $table) {
            if (Schema::hasColumn('section_component_subcomponents', 'is_multiple')) {
                $table->dropColumn('is_multiple');
            }
        });

        Schema::table('section_components', function (Blueprint $table) {
            if (Schema::hasColumn('section_components', 'is_multiple')) {
                $table->dropColumn('is_multiple');
            }
        });

        Schema::table('components', function (Blueprint $table) {
            if (Schema::hasColumn('components', 'is_multiple')) {
                $table->dropColumn('is_multiple');
            }
        });
    }
};
