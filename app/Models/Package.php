<?php

namespace App\Models;

use App\Helpers\Constant;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{

    protected $fillable = [
        'package_invitation_type',
        'free_invitations_count',
        'package_type',
        'active',
        'count',
        'price'
    ];

    public function scopeActive($query)
    {
        $query->where('active', Constant::CATEGORY_STATUS['Active']);
    }
    public function scopePackageType($query,$type)
    {
        $query->where('package_type', $type);
    }
    public function scopeInvitationPackageType($query,$type)
    {
        $query->where('package_invitation_type', $type);
    }
    public function invitations()
    {
        return $this->hasMany(InvitationPackage::class);
    }

}
