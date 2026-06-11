<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_request_id')->constrained('approval_requests')->onDelete('cascade');
            $table->string('decision'); // approved, rejected, revision_requested
            $table->foreignId('approver_id')->constrained('users')->onDelete('cascade');
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->index('approval_request_id');
            $table->index('approver_id');
            $table->unique('approval_request_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_decisions');
    }
};