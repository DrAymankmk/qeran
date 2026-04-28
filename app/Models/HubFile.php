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

    public function get_thumbnail_path()
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk(mediaDisk());
        return $disk->url(trim($this->bucket_name.'/thumbnail/'.$this->path, '/'));
    }

    public function get_medium_path()
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk(mediaDisk());
        return $disk->url(trim($this->bucket_name.'/medium/'.$this->path, '/'));
    }

    public function get_path()
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk(mediaDisk());
        return $disk->url(trim($this->bucket_name.'/'.$this->path, '/'));
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
