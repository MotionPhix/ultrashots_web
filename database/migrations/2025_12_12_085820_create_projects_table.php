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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullableOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('production_type')->nullable();
            $table->string('category')->nullable();
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'on_hold', 'cancelled'])->default('not_started');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('estimated_hours')->nullable();
            $table->integer('actual_hours')->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->json('technologies')->nullable();
            $table->json('features')->nullable();
            $table->text('challenges')->nullable();
            $table->text('solutions')->nullable();
            $table->json('process_steps')->nullable();
            $table->text('results')->nullable();
            $table->text('client_feedback')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_public')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('poster_url')->nullable();
            $table->string('live_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('figma_url')->nullable();
            $table->string('behance_url')->nullable();
            $table->string('dribbble_url')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('is_featured');
            $table->index('is_public');
            $table->index('created_at');
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
        Schema::dropIfExists('projects');
    }
};
