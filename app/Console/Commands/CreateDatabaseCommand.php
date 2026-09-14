<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PDO;

class CreateDatabaseCommand extends Command
{
    protected $signature = 'db:create';
    protected $description = 'Membuat database PostgreSQL dengan nama crm_db jika belum ada';

    public function handle()
    {
        $database = config('database.connections.pgsql.database');
        $host = config('database.connections.pgsql.host');
        $port = config('database.connections.pgsql.port');
        $username = config('database.connections.pgsql.username');
        $password = config('database.connections.pgsql.password');

        try {
            $pdo = new PDO("pgsql:host={$host};port={$port};dbname=postgres", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = $pdo->prepare("SELECT 1 FROM pg_database WHERE datname = :dbname");
            $query->execute(['dbname' => $database]);
            $exists = $query->fetchColumn();

            if (!$exists) {
                $pdo->exec("CREATE DATABASE \"{$database}\"");
                $this->info("Database '{$database}' berhasil dibuat!");
            } else {
                $this->info("Database '{$database}' sudah ada.");
            }
        } catch (\Exception $e) {
            $this->error("Gagal membuat database: " . $e->getMessage());
        }
    }
}