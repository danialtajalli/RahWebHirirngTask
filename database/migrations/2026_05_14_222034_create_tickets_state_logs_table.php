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
        Schema::create('tickets_state_logs', function (Blueprint $table) {

            $table->id();
            $table->timestamps();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->string('from_state')->nullable();
            $table->string('to_state');
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets_state_logs');
    }
};
