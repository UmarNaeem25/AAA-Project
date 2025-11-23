<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id');
            $table->foreignId('role_id');
            $table->foreignId('user_id')->nullable();
            $table->date('date');
            $table->time('from');
            $table->time('to');
            $table->float('duration'); 
            $table->float('break_time')->default(0); 
            $table->enum('status', ['open', 'unpublished', 'published', 'accepted'])->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
