<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->string('from_address');
            $table->string('to_address');
            $table->date('date');
            $table->string('time', 5)->nullable();
            $table->enum('size_label', ['document', 'small', 'medium', 'large']);
            $table->decimal('weight_kg', 8, 2)->nullable();
            $table->string('contents_summary');
            $table->enum('fragility', ['normal', 'fragile'])->default('normal');
            $table->boolean('documents_required')->default(false);
            $table->unsignedInteger('budget')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['open', 'matched', 'completed', 'cancelled'])->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agal_requests');
    }
};
