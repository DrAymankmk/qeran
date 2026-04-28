<?php

namespace App\Console\Commands;

use App\Models\HubFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateHubMediaToDisk extends Command
{
    protected $signature = 'media:migrate-hub-files
                            {--from=public : Source disk name}
                            {--to= : Target disk name (default: filesystems.media_disk)}
                            {--limit=0 : Max rows to process (0 = all)}
                            {--dry-run : Show actions without copying}
                            {--skip-existing : Skip files that already exist on target}';

    protected $description = 'Copy hub_files media objects between filesystem disks (e.g. public -> wasabi).';

    public function handle(): int
    {
        $from = (string) $this->option('from');
        $to = (string) ($this->option('to') ?: mediaDisk());
        $limit = (int) $this->option('limit');
        $dryRun = (bool) $this->option('dry-run');
        $skipExisting = (bool) $this->option('skip-existing');

        if ($from === $to) {
            $this->error('Source and target disks are the same.');
            return self::FAILURE;
        }

        $source = Storage::disk($from);
        $target = Storage::disk($to);

        $query = HubFile::query()->orderBy('id');
        if ($limit > 0) {
            $query->limit($limit);
        }

        $rows = $query->get();
        if ($rows->isEmpty()) {
            $this->info('No hub_files rows found.');
            return self::SUCCESS;
        }

        $copied = 0;
        $skipped = 0;
        $missing = 0;
        $failed = 0;

        foreach ($rows as $hubFile) {
            $keys = [
                trim($hubFile->bucket_name . '/' . $hubFile->path, '/'),
            ];

            if ((int) $hubFile->file_type === 1) {
                $keys[] = trim($hubFile->bucket_name . '/medium/' . $hubFile->path, '/');
                $keys[] = trim($hubFile->bucket_name . '/thumbnail/' . $hubFile->path, '/');
            }

            foreach ($keys as $key) {
                if (!$source->exists($key)) {
                    $missing++;
                    $this->warn("Missing on source: {$key}");
                    continue;
                }

                if ($skipExisting && $target->exists($key)) {
                    $skipped++;
                    continue;
                }

                if ($dryRun) {
                    $this->line("Would copy: {$key}");
                    continue;
                }

                try {
                    $target->put($key, $source->get($key));
                    $copied++;
                } catch (\Throwable $e) {
                    $failed++;
                    $this->error("Failed: {$key} ({$e->getMessage()})");
                }
            }
        }

        $this->newLine();
        $this->info("From disk: {$from}");
        $this->info("To disk: {$to}");
        $this->info("Copied: {$copied}");
        $this->info("Skipped: {$skipped}");
        $this->info("Missing: {$missing}");
        $this->info("Failed: {$failed}");

        if ($dryRun) {
            $this->comment('Dry run enabled: no files were copied.');
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
