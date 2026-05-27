<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE resources ADD COLUMN search_vector tsvector");

        DB::statement("
            UPDATE resources
            SET search_vector = to_tsvector('portuguese',
                coalesce(title, '') || ' ' ||
                coalesce(authors, '') || ' ' ||
                coalesce(description, '')
            )
        ");

        DB::statement("CREATE INDEX resources_search_vector_idx ON resources USING GIN(search_vector)");

        DB::statement("
            CREATE OR REPLACE FUNCTION resources_search_vector_update() RETURNS trigger AS $$
            BEGIN
                NEW.search_vector := to_tsvector('portuguese',
                    coalesce(NEW.title, '') || ' ' ||
                    coalesce(NEW.authors, '') || ' ' ||
                    coalesce(NEW.description, '')
                );
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql
        ");

        DB::statement("
            CREATE TRIGGER resources_search_vector_trigger
            BEFORE INSERT OR UPDATE ON resources
            FOR EACH ROW EXECUTE FUNCTION resources_search_vector_update()
        ");
    }

    public function down(): void
    {
        DB::statement("DROP TRIGGER IF EXISTS resources_search_vector_trigger ON resources");
        DB::statement("DROP FUNCTION IF EXISTS resources_search_vector_update");
        DB::statement("DROP INDEX IF EXISTS resources_search_vector_idx");
        DB::statement("ALTER TABLE resources DROP COLUMN IF EXISTS search_vector");
    }
};
