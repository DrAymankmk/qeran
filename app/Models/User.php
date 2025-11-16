<?php

namespace App\Models;

use App\Helpers\Constant;
use App\Services\External\CometChat;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public $appends = [];
    protected $fillable = [
        'account_type',
        'device_id',
        'verified',
        'name',
        'slug',
        'phone',
        'password',
        'country_code',
        'email',
        'latitude',
        'longitude',
        'address',
        'average_rate',
        'rate_count',
        'notification_count',
        'platform',
        'register_type',
        'description',
        'email_verified_at',
        'gender',
        'language'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function image()
    {
        $this->load('hubFiles');
        return $this->hubFiles ?->get_path();
    }

    public function scopeCheckUserExist($query, $phone)
    {
        return $query->where(['phone' => $phone])->where('deleted_at', null);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function hubFiles()
    {
        return $this->morphOne(HubFile::class, 'morphable');
    }

    public function myInvitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function invitedToInvitations()
    {
        return $this->belongsToMany(Invitation::class)
            ->withPivot('role', 'invitation_count');
    }
    public function invitedToUsers()
    {
        return $this->belongsToMany(User::class,'invitation_user','invited_by','user_id','id','id')
            ->withPivot('role', 'invitation_count','invitation_id');
    }

}
