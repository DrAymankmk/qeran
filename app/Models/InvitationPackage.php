<?php

namespace App\Models;

use App\Helpers\Constant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitationPackage extends Model
{
    use HasFactory;

    protected $table = 'invitation_package';
   protected $fillable = [
        'invitation_id',
        'package_id',
        'status',
        'count',
        'price'
    ];

    public function image()
    {
        $this->load('hubFiles');
        return $this->hubFiles()->where([
            'file_type' => Constant::FILE_TYPE['Image'],
            'file_key' => Constant::FILE_KEY['Receipt']
        ])->first()?->get_path();
    }

    public function imageMimeType()
    {
        $this->load('hubFiles');
        return $this->hubFiles()->where([
            'file_type' => Constant::FILE_TYPE['Image'],
            'file_key' => Constant::FILE_KEY['Main']
        ])->first()?->getMimeType;
    }

    public function hubFiles()
    {
        return $this->morphMany(HubFile::class, 'morphable')->orderBy('created_at', 'desc');
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
        
    }

    public function invitation()
    {
        return $this->belongsTo(Invitation::class);
    }

}
