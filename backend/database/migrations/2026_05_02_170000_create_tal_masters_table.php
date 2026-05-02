<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tal_masters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_id')->constrained('users')->cascadeOnDelete();
            $table->enum('category', ['beauty', 'home', 'repair']);
            $table->string('service_label', 120);
            $table->text('description');
            $table->string('location');
            $table->enum('availability_status', ['now', 'later', 'tomorrow', 'busy']);
            $table->string('available_note')->nullable();
            $table->unsignedInteger('price_from')->nullable();
            $table->enum('status', ['open', 'matched', 'completed', 'cancelled'])->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tal_masters');
    }
};
