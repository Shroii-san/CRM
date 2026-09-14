<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SqlFileSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/sql/crm_db.sql');

        if (!File::exists($path)) {
            $this->command->error("File SQL tidak ditemukan di: {$path}");
            return;
        }

        $host = config('database.connections.pgsql.host');
        $port = config('database.connections.pgsql.port');
        $database = config('database.connections.pgsql.database');
        $username = config('database.connections.pgsql.username');
        $password = config('database.connections.pgsql.password');

        $command = sprintf(
            'PGPASSWORD="%s" psql -h %s -p %s -U %s -d %s -f "%s"',
            $password,
            $host,
            $port,
            $username,
            $database,
            $path
        );

        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $resetSequencesQuery = "
                DO $$ 
                DECLARE 
                    r RECORD;
                BEGIN 
                    FOR r IN (
                        SELECT table_name, column_name 
                        FROM information_schema.columns 
                        WHERE column_default LIKE 'nextval%' 
                          AND table_schema = 'public'
                    ) LOOP 
                        EXECUTE format(
                            'SELECT setval(pg_get_serial_sequence(%L, %L), COALESCE(MAX(%I), 1)) FROM %I', 
                            r.table_name, r.column_name, r.column_name, r.table_name
                        );
                    END LOOP; 
                END $$;
            ";
            DB::statement($resetSequencesQuery);

            $this->command->info('Berhasil mengimpor data dan reset sequence!');
        } else {
            $this->command->error('Gagal mengimpor file SQL. Pastikan psql terinstall di terminal.');
        }
    }
}