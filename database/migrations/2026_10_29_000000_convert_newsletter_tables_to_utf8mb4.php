<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        // The same newsletter text is copied to notifications when sending.
        foreach (['newsletters', 'notifications'] as $table) {
            if (Schema::hasTable($table)) {
                $tableName = DB::connection()->getTablePrefix() . $table;
                $quotedTable = '`' . str_replace('`', '``', $tableName) . '`';

                DB::statement(
                    'ALTER TABLE ' . $quotedTable .
                    ' CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
                );
            }
        }
    }

    public function down(): void
    {
        // Retain utf8mb4: downgrading could destroy emojis saved after migration.
    }
};
