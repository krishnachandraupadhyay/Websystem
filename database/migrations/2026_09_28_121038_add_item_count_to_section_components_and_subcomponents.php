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
        Schema::table('section_components', function (Blueprint $table) {
            if (!Schema::hasColumn('section_components', 'item_count')) {
                $table->integer('item_count')->nullable()->after('is_multiple');
            }
        });

        Schema::table('section_component_subcomponents', function (Blueprint $table) {
            if (!Schema::hasColumn('section_component_subcomponents', 'item_count')) {
                $table->integer('item_count')->nullable()->after('is_multiple');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('section_components', function (Blueprint $table) {
            if (Schema::hasColumn('section_components', 'item_count')) {
                $table->dropColumn('item_count');
            }
        });

        Schema::table('section_component_subcomponents', function (Blueprint $table) {
            if (Schema::hasColumn('section_component_subcomponents', 'item_count')) {
                $table->dropColumn('item_count');
            }
        });
    }
};
