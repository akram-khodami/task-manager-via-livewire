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
        Schema::create('tasks', function (Blueprint $table) {

            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['todo', 'in_progress', 'done', 'cancelled'])->default('todo');

            $table->dateTime('due_date')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->foreignId('folder_id')->nullable()->constrained('folders')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('assigned_to')->constrained('users')->nullable();
            $table->decimal('estimated_hours', 8, 2)->default(0);
            $table->decimal('spent_hours', 8, 2)->default(0);
            $table->timestamps();

            $table->index(['folder_id', 'project_id', 'created_by', 'status', 'priority'], 'tasks_main_filter_index');

            $table->index(['title'], 'tasks_title_index');

            $table->index(['due_date'], 'tasks_due_date_index');

            $table->fullText(['title', 'description'], 'tasks_fulltext_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
