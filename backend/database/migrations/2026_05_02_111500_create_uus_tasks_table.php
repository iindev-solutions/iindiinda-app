<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('uus_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->enum('category', ['home', 'repair', 'delivery', 'other']);
            $table->text('description');
            $table->string('location');
            $table->enum('desired_when', ['today', 'tomorrow', 'date', 'flexible']);
            $table->date('date')->nullable();
            $table->unsignedInteger('budget')->nullable();
            $table->enum('budget_type', ['fixed', 'negotiable']);
            $table->enum('urgency', ['urgent', 'normal']);
            $table->unsignedTinyInteger('response_limit');
            $table->enum('status', ['open', 'matched', 'completed', 'cancelled'])->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uus_tasks');
    }
};
