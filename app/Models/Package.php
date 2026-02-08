<?php

namespace App\Models;

use App\Helpers\Constant;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use Translatable;

    public $translatedAttributes = ['title', 'subtitle', 'content'];
    public $translationModel = PackageTranslation::class;

    protected $fillable = [
        'package_invitation_type',
        'free_invitations_count',
        'package_type',
        'active',
        'count',
        'price',
        'title',
        'subtitle',
        'content'
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

    public function scopeFreePackage($query)
    {
        $query->where('package_type', Constant::PACKAGE_TYPE['Free Package']);
    }

    public function scopeExcludeUsedFreePackagesForUser($query, int $userId)
    {
        $usedFreePackageIds = InvitationPackage::query()
            ->whereHas('invitation', fn ($q) => $q->where('user_id', $userId))
            ->whereHas('package', fn ($q) => $q->freePackage())
            ->pluck('package_id');

        if ($usedFreePackageIds->isNotEmpty()) {
            $query->whereNotIn('id', $usedFreePackageIds);
        }
    }
    public function invitations()
    {
        return $this->hasMany(InvitationPackage::class);
    }

}
