<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    use HasFactory, Translatable;

    public $translatedAttributes = ['name'];
    public $translationModel = DesignTranslation::class;

    protected $fillable = [
        'category_id',
        'code',
        'show_on',
    ];

    protected $casts = [
        'show_on' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function image()
    {
        $this->load('hubFiles');
        return $this->hubFiles?->get_path();
    }

    public function isVideoMedia(): bool
    {
        $this->loadMissing('hubFiles');
        $mime = $this->hubFiles?->getMimeType ?? '';
        if (is_string($mime) && str_starts_with($mime, 'video/')) {
            return true;
        }
        $ext = strtolower((string) ($this->hubFiles?->extension ?? ''));
        if ($ext === '' && $this->hubFiles && $this->hubFiles->path) {
            $ext = strtolower(pathinfo($this->hubFiles->path, PATHINFO_EXTENSION));
        }

        return in_array($ext, ['mp4', 'webm', 'ogg', 'ogv', 'mov', 'm4v'], true);
    }

    public function hubFiles()
    {
        return $this->morphOne(HubFile::class, 'morphable');
    }
}
