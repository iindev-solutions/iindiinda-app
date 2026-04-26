<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agal_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrier_id')->constrained('users')->cascadeOnDelete();
            $table->string('from_address');
            $table->string('to_address');
            $table->date('date');
            $table->string('time', 5)->nullable();
            $table->enum('size_label', ['document', 'small', 'medium', 'large']);
            $table->decimal('weight_kg_max', 8, 2)->nullable();
            $table->text('accepted_items')->nullable();
            $table->text('restricted_items')->nullable();
            $table->unsignedInteger('price')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['open', 'matched', 'completed', 'cancelled'])->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agal_routes');
    }
};
