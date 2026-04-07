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
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // 'like', 'recommended', 'nice', 'feedback'
            $table->text('feedback')->nullable(); // for 'feedback' type
            $table->string('visitor_name')->nullable(); // for anonymous reactions
            $table->string('visitor_email')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'type']);
            $table->unique(['project_id', 'visitor_email', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};
