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
        Schema::table('tickets', function (Blueprint $table) {

            $table->string('attachment')->nullable();

            $table->text('admin1_comment')->nullable();
            $table->text('admin2_comment')->nullable();

            $table->timestamp('admin1_action_at')->nullable();
            $table->timestamp('admin2_action_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {

            $table->dropColumn([
                'attachment',
                'admin1_comment',
                'admin2_comment',
                'admin1_action_at',
                'admin2_action_at'
            ]);
        });
    }
};
