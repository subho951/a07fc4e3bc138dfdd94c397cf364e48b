<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_reg_events', function (Blueprint $table) {
            if (!Schema::hasColumn('user_reg_events', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable();
            }

            if (!Schema::hasColumn('user_reg_events', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable();
            }

            if (!Schema::hasColumn('user_reg_events', 'location')) {
                $table->text('location')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_reg_events', function (Blueprint $table) {
            if (Schema::hasColumn('user_reg_events', 'latitude')) {
                $table->dropColumn('latitude');
            }

            if (Schema::hasColumn('user_reg_events', 'longitude')) {
                $table->dropColumn('longitude');
            }

            if (Schema::hasColumn('user_reg_events', 'location')) {
                $table->dropColumn('location');
            }
        });
    }
};
