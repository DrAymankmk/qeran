<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HubFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'morphable_id',
        'morphable_type',
        'file_type',
        'file_key',
        'path',
        'bucket_name',
        'extension',
        'size',
        'original_name',
        'getMimeType',
    ];


    public function morphable_id()
    {
        return $this->morphTo();
    }

    public function get_real_url()
    {
        return \Storage::disk($this->bucket_name)->url(strtolower($this->visibility).$this->path.$this->name);
    }

    public function get_folder_file()
    {
        return $this->bucket_name.'/'.$this->path;
    }

    public function get_thumbnail_path()
    {
        return asset('storage/' . $this->bucket_name.'/thumbnail/'.$this->path);
    }

    public function get_medium_path()
    {
        return asset('storage/' . $this->bucket_name.'/medium/'.$this->path);
    }

    public function get_path()
    {
        return asset('storage/' . $this->bucket_name.'/'.$this->path);
    }

    public function get_size()
    {
        return \Storage::disk($this->bucket_name)->size(strtolower($this->visibility).$this->path.$this->name);
    }

    public function download()
    {
        return \Storage::disk($this->bucket_name)->download(strtolower($this->visibility).$this->path.$this->name);
    }
}
