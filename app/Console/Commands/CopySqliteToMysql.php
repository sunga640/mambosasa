<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class CopySqliteToMysql extends Command
{
    protected $signature = 'db:copy-sqlite-to-mysql {--source=sqlite_old} {--target=mysql} {--chunk=200}';

    protected $description = 'Copy all application data from the SQLite database into the MySQL database with row-count verification.';

    public function handle(): int
    {
        $source = (string) $this->option('source');
        $target = (string) $this->option('target');
        $chunkSize = max(1, (int) $this->option('chunk'));

        $sourceDb = DB::connection($source);
        $targetDb = DB::connection($target);

        $tables = collect($sourceDb->select("
            SELECT name
            FROM sqlite_master
            WHERE type = 'table'
              AND name NOT LIKE 'sqlite_%'
            ORDER BY name
        "))
            ->map(fn ($row) => (string) $row->name)
            ->reject(fn (string $table) => $table === 'migrations')
            ->values();

        if ($tables->isEmpty()) {
            $this->error('No source tables were found in SQLite.');

            return self::FAILURE;
        }

        $this->info('Copying '.count($tables).' table(s) from '.$source.' to '.$target.'.');

        $targetDb->statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            foreach ($tables as $table) {
                $count = (int) $sourceDb->table($table)->count();
                $this->line("Preparing {$table} ({$count} row(s))...");

                $targetDb->table($table)->truncate();

                if ($count === 0) {
                    continue;
                }

                $rows = $sourceDb->table($table)->get();
                $payload = [];

                foreach ($rows as $row) {
                    $payload[] = (array) $row;

                    if (count($payload) >= $chunkSize) {
                        $targetDb->table($table)->insert($payload);
                        $payload = [];
                    }
                }

                if ($payload !== []) {
                    $targetDb->table($table)->insert($payload);
                }

                $targetCount = (int) $targetDb->table($table)->count();
                if ($targetCount !== $count) {
                    throw new \RuntimeException("Row count mismatch for {$table}: source={$count}, target={$targetCount}");
                }

                $this->info("Copied {$table}: {$targetCount} row(s).");
            }
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        } finally {
            $targetDb->statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->info('SQLite to MySQL copy completed successfully.');

        return self::SUCCESS;
    }
}
