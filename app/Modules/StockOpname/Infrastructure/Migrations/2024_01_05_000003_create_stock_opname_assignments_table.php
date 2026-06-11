<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opname_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_session_id')->constrained('stock_opname_sessions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();

            $table->index('stock_opname_session_id');
            $table->index('user_id');
            $table->unique(['stock_opname_session_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_assignments');
    }
};