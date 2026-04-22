<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('trip_id')->nullable()->constrained('trips')->nullOnDelete();
            $table->foreignId('request_id')->nullable()->constrained('requests')->nullOnDelete();
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();
        });

        DB::statement(
            'ALTER TABLE responses ADD CONSTRAINT responses_target_check CHECK ((trip_id IS NOT NULL AND request_id IS NULL) OR (trip_id IS NULL AND request_id IS NOT NULL))'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('responses');
    }
};
