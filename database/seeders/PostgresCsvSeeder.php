<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class PostgresCsvSeeder extends Seeder
{
    /**
     * Ordem das tabelas a serem importadas considerando chaves estrangeiras.
     */
    protected array $tables = [
        'roles',
        'permissions',
        'model_has_roles',
        'model_has_permissions',
        'role_has_permissions',
        'teams',
        'users',
        'team_members',
        'academic_terms',
        'courses',
        'teachers',
        'students',
        'course_classes',
        'disciplines',
        'course_class_disciplines',
        'enrollments',
        'class_enrollments',
        'evaluations',
        'feedback',
        'audits',
        'pulse_aggregates',
        'pulse_entries',
        'pulse_values',
    ];

    public function run(): void
    {
        $dirPath = database_path('seeders/postgres');

        if (! File::isDirectory($dirPath)) {
            $this->command->error("Diretório {$dirPath} não encontrado.");

            return;
        }

        $isPgsql = DB::getDriverName() === 'pgsql';

        if ($isPgsql) {
            DB::statement("SET session_replication_role = 'replica';");
        } else {
            Schema::disableForeignKeyConstraints();
        }

        foreach ($this->tables as $table) {
            $filePath = "{$dirPath}/{$table}.csv";

            if (! File::exists($filePath)) {
                continue;
            }

            if (! Schema::hasTable($table)) {
                $this->command->warn("Tabela '{$table}' não existe no banco, ignorando.");

                continue;
            }

            $this->command->info("Semeando tabela a partir do CSV: {$table}");
            try {
                if ($isPgsql) {
                    DB::statement("TRUNCATE TABLE \"{$table}\" RESTART IDENTITY CASCADE;");
                } else {
                    DB::table($table)->truncate();
                }
            } catch (\Throwable $e) {
                DB::table($table)->delete();
            }

            try {
                $this->importCsvToTable($table, $filePath);
            } catch (\Throwable $e) {
                $this->command->error("Erro ao semear a tabela [{$table}]: ".$e->getMessage());
                throw $e;
            }

            if ($isPgsql && Schema::hasColumn($table, 'id')) {
                try {
                    $maxId = DB::table($table)->max('id');
                    if ($maxId !== null) {
                        DB::statement("SELECT setval(pg_get_serial_sequence('{$table}', 'id'), {$maxId})");
                    }
                } catch (\Throwable) {
                    // Ignora falhas de atualização de sequence em tabelas sem sequence id padrão
                }
            }
        }

        if ($isPgsql) {
            DB::statement("SET session_replication_role = 'origin';");
        } else {
            Schema::enableForeignKeyConstraints();
        }

        $this->command->info('População do banco com dados reais do Postgres concluída com sucesso!');
    }

    private function importCsvToTable(string $table, string $filePath): void
    {
        $handle = fopen($filePath, 'rb');
        if ($handle === false) {
            return;
        }

        $header = fgetcsv($handle, escape: '\\');
        if (! $header) {
            fclose($handle);

            return;
        }

        // Remove BOM UTF-8 se presente no cabeçalho
        if (isset($header[0])) {
            $header[0] = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $header[0]);
        }

        $batch = [];
        $chunkSize = 500;

        while (($row = fgetcsv($handle, escape: '\\')) !== false) {
            if (count($row) !== count($header)) {
                continue;
            }

            // Ignora linhas de cabeçalho duplicadas no meio do arquivo CSV
            if ($row[0] === $header[0] && isset($header[1]) && $row[1] === $header[1]) {
                continue;
            }

            $record = [];
            foreach ($header as $index => $column) {
                $val = $row[$index];

                if ($val === '' || $val === null) {
                    $record[$column] = null;
                } elseif (strtoupper($val) === 'TRUE') {
                    $record[$column] = true;
                } elseif (strtoupper($val) === 'FALSE') {
                    $record[$column] = false;
                } else {
                    $record[$column] = $val;
                }
            }

            $batch[] = $record;

            if (count($batch) >= $chunkSize) {
                try {
                    DB::table($table)->insertOrIgnore($batch);
                } catch (\Throwable $e) {
                    $this->command->warn("Aviso ao inserir lote na tabela {$table}: ".$e->getMessage());
                }
                $batch = [];
            }
        }

        if ($batch !== []) {
            try {
                DB::table($table)->insertOrIgnore($batch);
            } catch (\Throwable $e) {
                $this->command->warn("Aviso ao inserir último lote na tabela {$table}: ".$e->getMessage());
            }
        }

        fclose($handle);
    }
}
