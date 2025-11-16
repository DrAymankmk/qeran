<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_type',
        'status',
        'name',
        'email',
        'country_code',
        'phone',
        'subject',
        'message',
        'user_id',
    ];

    public function reason()
    {
        return $this->belongsTo(Setting::class, 'reason_id');
    }

    public function scopeSearchBetweenDates($query, $from, $to)
    {
        return $query->when($from && $to, fn ($query) => $query->whereBetween('created_at', [$from, $to]));
    }

    public function scopeLastMonth($query)
    {
        $query->whereMonth('created_at', now()->subMonth()->month);
    }
    public function image()
    {
        $this->load('hubFiles');

        return $this->hubFiles ? $this->hubFiles->get_path() : 'website/images/blog/02.jpg';
    }
    public function hubFiles()
    {
        return $this->morphOne(HubFile::class, 'morphable');
    }

}
