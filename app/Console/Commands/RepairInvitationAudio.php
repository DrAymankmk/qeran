<?php

namespace App\Console\Commands;

use App\Helpers\Constant;
use App\Models\HubFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RepairInvitationAudio extends Command
{
    protected $signature = 'media:repair-invitation-audio
                            {--limit=200 : Max files to process}
                            {--dry-run : Show what would change without writing}
                            {--only-mp3 : Only target records saved as .mp3}';

    protected $description = 'Transcode invitation audio files to real MP3 for browser playback.';

    public function handle(): int
    {
        if (! function_exists('exec')) {
            $this->error('Cannot repair audio: PHP function exec() is disabled on this server.');
            $this->line('Fix options:');
            $this->line('- Enable exec() (and install ffmpeg) on the server, then rerun this command.');
            $this->line('- Or convert the audio files offline to real MP3 and re-upload them.');
            return self::FAILURE;
        }

        $limit = (int) $this->option('limit');
        $dryRun = (bool) $this->option('dry-run');
        $onlyMp3 = (bool) $this->option('only-mp3');

        $this->info('Scanning invitation audio hub_files…');

        $q = HubFile::query()
            ->where('bucket_name', Constant::INVITATION_AUDIO_FOLDER_NAME)
            ->where('file_type', Constant::FILE_TYPE['Audio'])
            ->orderBy('id', 'desc');

        if ($onlyMp3) {
            $q->where(function ($qq) {
                $qq->where('extension', 'mp3')->orWhere('path', 'like', '%.mp3');
            });
        }

        $files = $q->limit($limit)->get();
        if ($files->isEmpty()) {
            $this->info('No files found.');
            return self::SUCCESS;
        }

        $processed = 0;
        $converted = 0;
        $skippedMissing = 0;
        $failed = 0;
        $disk = Storage::disk(mediaDisk());

        foreach ($files as $hub) {
            $processed++;

            $rel = $hub->bucket_name.'/'.$hub->path;
            if (! $disk->exists($rel)) {
                $skippedMissing++;
                $this->warn("Missing: {$rel} (hub_file id {$hub->id})");
                continue;
            }

            $tempDir = storage_path('app/tmp/audio-repair');
            if (!is_dir($tempDir)) {
                @mkdir($tempDir, 0775, true);
            }

            $sourceAbs = $tempDir.'/'.basename($rel);
            file_put_contents($sourceAbs, $disk->get($rel));
            $baseName = pathinfo($hub->path, PATHINFO_FILENAME);
            $targetRel = $hub->bucket_name.'/'.$baseName.'.mp3';
            $targetAbs = $tempDir.'/'.$baseName.'.mp3';

            // If already a clean mp3 (by metadata), skip
            $metaExt = strtolower((string) ($hub->extension ?? ''));
            $metaMime = strtolower((string) ($hub->getMimeType ?? ''));
            if ($metaExt === 'mp3' && ($metaMime === '' || str_contains($metaMime, 'audio/mpeg'))) {
                continue;
            }

            $this->line("Convert: {$rel} -> {$targetRel}");

            if ($dryRun) {
                continue;
            }

            // Transcode with ffmpeg
            $cmd = 'ffmpeg -y -i '.escapeshellarg($sourceAbs).' -vn -acodec libmp3lame -q:a 4 '.escapeshellarg($targetAbs).' 2>&1';
            \exec($cmd, $out, $code);

            if ($code !== 0 || ! file_exists($targetAbs) || filesize($targetAbs) === 0) {
                $failed++;
                $this->error("Failed (id {$hub->id}). ffmpeg exit {$code}");
                @unlink($sourceAbs);
                @unlink($targetAbs);
                continue;
            }

            $mp3Bytes = file_get_contents($targetAbs);
            if ($mp3Bytes === false) {
                $failed++;
                $this->error("Failed (id {$hub->id}). Could not read transcoded file.");
                @unlink($sourceAbs);
                @unlink($targetAbs);
                continue;
            }

            $disk->put($targetRel, $mp3Bytes);

            // Update DB to point to the mp3
            $hub->path = $baseName.'.mp3';
            $hub->extension = 'mp3';
            $hub->getMimeType = 'audio/mpeg';
            $hub->size = $disk->size($targetRel);
            $hub->save();

            // Remove original if it wasn't already the mp3 file
            if ($rel !== $targetRel) {
                $disk->delete($rel);
            }

            $converted++;
            @unlink($sourceAbs);
            @unlink($targetAbs);
        }

        $this->newLine();
        $this->info("Processed: {$processed}");
        $this->info("Converted: {$converted}");
        $this->info("Missing: {$skippedMissing}");
        $this->info("Failed: {$failed}");

        if ($dryRun) {
            $this->comment('Dry run: no files were modified.');
        }

        return self::SUCCESS;
    }
}

