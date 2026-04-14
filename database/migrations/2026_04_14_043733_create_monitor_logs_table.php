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
        Schema::create('monitors_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monitor_id');
            $table->enum('status', ['UP', 'DOWN']);
            $table->float('response_time')->nullable();
            $table->integer('status_code')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('checked_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitors_log');
    }
};
