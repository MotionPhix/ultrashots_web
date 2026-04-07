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
        Schema::table('projects', function (Blueprint $table) {
            $table->index(['is_public', 'status'], 'projects_public_status_index');
            $table->index(['is_public', 'category'], 'projects_public_category_index');
            $table->index(['is_public', 'production_type'], 'projects_public_production_type_index');
            $table->index(['customer_id', 'status'], 'projects_customer_status_index');
            $table->index(['status', 'priority'], 'projects_status_priority_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('projects_public_status_index');
            $table->dropIndex('projects_public_category_index');
            $table->dropIndex('projects_public_production_type_index');
            $table->dropIndex('projects_customer_status_index');
            $table->dropIndex('projects_status_priority_index');
        });
    }
};
