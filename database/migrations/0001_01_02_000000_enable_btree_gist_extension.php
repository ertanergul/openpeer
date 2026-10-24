<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Baseline requirement (R-01): btree_gist unlocks GIST-backed exclusion
     * constraints needed by later domain tables (non-overlapping bookings and
     * similar uniqueness windows). Creating it along the first migration
     * group keeps fresh PostgreSQL clusters ready from day one.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('CREATE EXTENSION IF NOT EXISTS btree_gist');
    }

    /**
     * Dropping is intentionally symmetric: no baseline table depends on the
     * extension yet, so a rollback leaves the cluster in its original state.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP EXTENSION IF EXISTS btree_gist');
    }
};
