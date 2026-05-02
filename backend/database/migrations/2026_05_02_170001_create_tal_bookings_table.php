<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tal_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tal_master_id')->constrained('tal_masters')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('message')->nullable();
            $table->string('desired_time')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();

            $table->unique(['tal_master_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tal_bookings');
    }
};
