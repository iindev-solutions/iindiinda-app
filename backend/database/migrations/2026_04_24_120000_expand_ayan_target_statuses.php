<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("UPDATE trips SET status = 'completed' WHERE status = 'closed'");
            DB::statement("UPDATE requests SET status = 'completed' WHERE status = 'closed'");
            DB::statement("ALTER TABLE trips MODIFY status ENUM('open', 'matched', 'completed', 'cancelled') NOT NULL DEFAULT 'open'");
            DB::statement("ALTER TABLE requests MODIFY status ENUM('open', 'matched', 'completed', 'cancelled') NOT NULL DEFAULT 'open'");
            return;
        }

        $this->rebuildTripsTableForSqlite();
        $this->rebuildRequestsTableForSqlite();
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("UPDATE trips SET status = 'open' WHERE status = 'matched'");
            DB::statement("UPDATE trips SET status = 'closed' WHERE status IN ('completed', 'cancelled')");
            DB::statement("UPDATE requests SET status = 'open' WHERE status = 'matched'");
            DB::statement("UPDATE requests SET status = 'closed' WHERE status IN ('completed', 'cancelled')");
            DB::statement("ALTER TABLE trips MODIFY status ENUM('open', 'closed') NOT NULL DEFAULT 'open'");
            DB::statement("ALTER TABLE requests MODIFY status ENUM('open', 'closed') NOT NULL DEFAULT 'open'");
            return;
        }

        $this->rebuildTripsTableForSqlite(true);
        $this->rebuildRequestsTableForSqlite(true);
    }

    private function rebuildTripsTableForSqlite(bool $down = false): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('ALTER TABLE trips RENAME TO trips_old');

        Schema::create('trips', function (Blueprint $table) use ($down) {
            $table->id();
            $table->foreignId('driver_id')->constrained('users')->cascadeOnDelete();
            $table->string('from_address');
            $table->string('to_address');
            $table->date('date');
            $table->string('time', 5);
            $table->unsignedTinyInteger('seats');
            $table->unsignedInteger('price');
            $table->text('comment')->nullable();
            $table->enum('status', $down ? ['open', 'closed'] : ['open', 'matched', 'completed', 'cancelled'])->default('open');
            $table->timestamps();
        });

        DB::statement(
            "INSERT INTO trips (id, driver_id, from_address, to_address, date, time, seats, price, comment, status, created_at, updated_at)
            SELECT id, driver_id, from_address, to_address, date, time, seats, price, comment,
            CASE
                WHEN status = 'closed' AND " . ($down ? '1 = 1' : '1 = 0') . " THEN 'closed'
                WHEN status = 'closed' THEN 'completed'
                WHEN status = 'matched' AND " . ($down ? '1 = 1' : '1 = 0') . " THEN 'open'
                WHEN status IN ('completed', 'cancelled') AND " . ($down ? '1 = 1' : '1 = 0') . " THEN 'closed'
                ELSE status
            END,
            created_at, updated_at FROM trips_old"
        );

        Schema::drop('trips_old');
        Schema::enableForeignKeyConstraints();
    }

    private function rebuildRequestsTableForSqlite(bool $down = false): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('ALTER TABLE requests RENAME TO requests_old');

        Schema::create('requests', function (Blueprint $table) use ($down) {
            $table->id();
            $table->foreignId('passenger_id')->constrained('users')->cascadeOnDelete();
            $table->string('from_address');
            $table->string('to_address');
            $table->date('date');
            $table->string('time', 5)->nullable();
            $table->text('description')->nullable();
            $table->enum('status', $down ? ['open', 'closed'] : ['open', 'matched', 'completed', 'cancelled'])->default('open');
            $table->timestamps();
        });

        DB::statement(
            "INSERT INTO requests (id, passenger_id, from_address, to_address, date, time, description, status, created_at, updated_at)
            SELECT id, passenger_id, from_address, to_address, date, time, description,
            CASE
                WHEN status = 'closed' AND " . ($down ? '1 = 1' : '1 = 0') . " THEN 'closed'
                WHEN status = 'closed' THEN 'completed'
                WHEN status = 'matched' AND " . ($down ? '1 = 1' : '1 = 0') . " THEN 'open'
                WHEN status IN ('completed', 'cancelled') AND " . ($down ? '1 = 1' : '1 = 0') . " THEN 'closed'
                ELSE status
            END,
            created_at, updated_at FROM requests_old"
        );

        Schema::drop('requests_old');
        Schema::enableForeignKeyConstraints();
    }
};
