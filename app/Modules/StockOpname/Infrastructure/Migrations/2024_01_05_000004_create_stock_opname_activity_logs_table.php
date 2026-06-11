<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opname_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_session_id')->constrained('stock_opname_sessions')->onDelete('cascade');
            $table->string('action'); // created, assigned, counting_started, submitted, reviewed, approved, item_counted
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('stock_opname_session_id');
            $table->index('action');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_activity_logs');
    }
};