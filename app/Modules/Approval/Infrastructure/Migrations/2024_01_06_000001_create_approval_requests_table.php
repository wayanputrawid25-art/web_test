<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['stock_opname', 'stock_adjustment', 'inventory_correction', 'manual_adjustment']);
            $table->enum('status', ['pending', 'approved', 'rejected', 'revision_requested'])->default('pending');
            $table->unsignedBigInteger('reference_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('requester_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('approver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('status');
            $table->index('requester_id');
            $table->index('approver_id');
            $table->index('reference_id');
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_requests');
    }
};