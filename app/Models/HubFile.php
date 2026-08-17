<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class HubFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'morphable_id',
        'morphable_type',
        'created_by_id',
        'created_by_type',
        'file_type',
        'file_key',
        'path',
        'bucket_name',
        'extension',
        'size',
        'original_name',
        'getMimeType',
    ];

    protected static function booted(): void
    {
        static::creating(function (HubFile $hubFile) {
            if ($hubFile->created_by_id !== null || $hubFile->created_by_type !== null) {
                return;
            }
            $attrs = hubFileCreatorAttributes();
            if ($attrs !== []) {
                $hubFile->fill($attrs);
            }
        });
    }

    public function morphable_id()
    {
        return $this->morphTo();
    }

    public function createdBy(): MorphTo
    {
        return $this->morphTo();
    }

    public function get_real_url()
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk($this->bucket_name);
        return $disk->url(strtolower($this->visibility).$this->path.$this->name);
    }

    public function get_folder_file()
    {
        return $this->bucket_name.'/'.$this->path;
    }

    public function get_thumbnail_path(): ?string
    {
        if (empty($this->path)) {
            return null;
        }

        return $this->mediaUrlForKey(trim($this->bucket_name.'/thumbnail/'.$this->path, '/'));
    }

    public function get_medium_path(): ?string
    {
        if (empty($this->path)) {
            return null;
        }

        return $this->mediaUrlForKey(trim($this->bucket_name.'/medium/'.$this->path, '/'));
    }

    public function get_path(): ?string
    {
        if (empty($this->path)) {
            return null;
        }

        return $this->mediaUrlForKey(trim($this->bucket_name.'/'.$this->path, '/'));
    }

    /**
     * Public URL that does not depend on Storage::exists() (often false on S3/Wasabi).
     */
    public function durablePublicUrl(): ?string
    {
        if (empty($this->path) || empty($this->bucket_name)) {
            return null;
        }

        $key = trim($this->bucket_name.'/'.$this->path, '/');
        $resolved = $this->mediaUrlForKey($key);
        if (is_string($resolved) && $resolved !== '') {
            return $resolved;
        }

        $diskName = mediaDisk();
        $driver = (string) config("filesystems.disks.{$diskName}.driver", 'local');
        $configuredUrl = rtrim((string) config("filesystems.disks.{$diskName}.url", ''), '/');

        if ($configuredUrl !== '') {
            return $configuredUrl.'/'.ltrim($key, '/');
        }

        try {
            $disk = Storage::disk($diskName);
            if ($driver === 's3') {
                return $disk->temporaryUrl($key, now()->addDays(7));
            }

            return $disk->url($key);
        } catch (\Throwable) {
            return Storage::disk('public')->url($key);
        }
    }

    protected function mediaUrlForKey(string $key): ?string
    {
        if ($key === '') {
            return null;
        }

        $diskName = mediaDisk();
        $driver = (string) config("filesystems.disks.{$diskName}.driver", 'local');
        $disk = Storage::disk($diskName);

        if ($driver === 's3') {
            try {
                if ($disk->exists($key)) {
                    return $disk->temporaryUrl($key, now()->addMinutes(30));
                }
            } catch (\Throwable) {
                // Fall through to public disk lookup below.
            }
        } else {
            if ($disk->exists($key)) {
                return $disk->url($key);
            }
        }

        $publicDisk = Storage::disk('public');
        if ($publicDisk->exists($key)) {
            return $publicDisk->url($key);
        }

        return null;
    }

    public function get_size()
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk($this->bucket_name);
        return $disk->size(strtolower($this->visibility).$this->path.$this->name);
    }

    public function download()
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk($this->bucket_name);
        return $disk->download(strtolower($this->visibility).$this->path.$this->name);
    }
}
