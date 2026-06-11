<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'assigned', 'in_progress', 'review', 'approved', 'closed'])->default('draft');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->foreignId('assignee_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->foreignId('inventory_transaction_id')->nullable()->constrained('inventory_transactions')->onDelete('set null');
            $table->date('due_date')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('priority');
            $table->index('assignee_id');
            $table->index('creator_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};