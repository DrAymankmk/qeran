<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $guard = 'admin';

    protected $guard_name = 'admin';

    protected $table="admins";

    protected $fillable = [
        'name', 'email', 'password', 'active'
    ];
    protected $hidden = [
        'password','created_at','updated_at'
    ];
    public function image()
    {
        $this->load('hubFiles');
        return $this->hubFiles?$this->hubFiles->get_path():asset('admin_assets/images/admin.png');
    }
    public function hubFiles()
    {
        return $this->morphOne(HubFile::class, 'morphable');
    }

}
