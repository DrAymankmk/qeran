<?php

namespace App\Models;

use App\Helpers\Constant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_type',
        'invitation_step',
        'category_id',
        'user_id',
        'paid',
        'status',
        'host_name',
        'name',
        'slug',
        'date',
        'time',
        'latitude',
        'longitude',
        'address',
        'groom',
        'bride',
        'groom_father',
        'bride_father',
        'event_name',
        'count',
        'price',
        'description',
        'package_id',
        'invitation_media_type',
        'code'
    ];

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($invitation) {
            \Log::info('Invitation creating event triggered', [
                'current_code' => $invitation->code,
                'is_empty' => empty($invitation->code)
            ]);
            
            if (empty($invitation->code)) {
                $invitation->code = static::generateUniqueCode();
                \Log::info('Generated new code', ['code' => $invitation->code]);
            } else {
                \Log::info('Code already exists, not generating', ['existing_code' => $invitation->code]);
            }
        });
    }

    /**
     * Generate a unique code for the invitation
     */
    public static function generateUniqueCode()
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (static::where('code', $code)->exists());
        
        return $code;
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

    public function designImage()
    {

        $this->load('hubFiles');
        return $this->hubFiles()->where([
            'file_type' => Constant::FILE_TYPE['Image'],
//            'file_key' => Constant::FILE_KEY['Not Main']
        ])
            ->orderBy('id', 'desc')
            ->first()?->get_path();


    }

    public function getMainImagePath()
    {
        $this->load('hubFiles');
        return $this->hubFiles()->where([
            'file_type' => Constant::FILE_TYPE['Image'],
            'file_key' => Constant::FILE_KEY['Not Main']
        ])->first()?->get_path();
    }



    public function designVideo()
    {
        $this->load('hubFiles');
        return $this->hubFiles()->where([
            'file_type' => Constant::FILE_TYPE['Video'],
            'file_key' => Constant::FILE_KEY['Not Main']
        ])->first()?->get_path();


    }

    public function designAudio()
    {
        $this->load('hubFiles');
        return $this->hubFiles()->where([
            'file_type' => Constant::FILE_TYPE['Audio'],
            'file_key' => Constant::FILE_KEY['Not Main']
        ])->first()?->get_path();


    }

    public function receiptImage()
    {
        $this->load('currentPackage');
        return $this->currentPackage?->image();


    }

    public function image()
    {
        $this->load('hubFiles');
        return $this->hubFiles()->where([
            'file_type' => Constant::FILE_TYPE['Image'],
            'file_key' => Constant::FILE_KEY['Main']
        ])->first()?->get_path();
    }

    public function qr($invitation_id, $user_id = null)
    {
        if (!$user_id) {
            return asset('storage/' . 'qr-code/Qr-' . $invitation_id . '-' . auth()->id() . '.png');
        }
        return asset('storage/' . 'qr-code/Qr-' . $invitation_id . '-' . $user_id . '.png');

    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeGetInvitationByType($query, $type)
    {
        return $query->where(['invitation_type' => $type]);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->whereIn('status', $status);
    }

    public function usersByRole($role)
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'invitation_count', 'seen', 'name','password', 'host_name')
            ->wherePivot('role', $role)
            ->withTimestamps();

    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role', 'invitation_count', 'seen', 'invited_by', 'name'])
            ->wherePivot('role', Constant::INVITATION_USER_ROLE['User'])
            ->withTimestamps();

    }

    public function guards()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'invitation_count', 'seen', 'name','password')
            ->wherePivotIn('role', [Constant::INVITATION_USER_ROLE['Guard'], Constant::INVITATION_USER_ROLE['Extra Guard']])
            ->withTimestamps();

    }

    public function admins()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'invitation_count', 'seen', 'name')
            ->wherePivot('role', Constant::INVITATION_USER_ROLE['Admin'])
            ->withTimestamps();

    }



    public function invitationPackages()
    {
        return $this->hasMany(InvitationPackage::class)->with('package');
    }

    public function paidPackages()
    {
        return $this->hasMany(InvitationPackage::class)->where('status',Constant::PAID_STATUS['Paid']);

    }

    public function paidInvitationPackages()
    {
        return $this->hasMany(InvitationPackage::class)->where('status', Constant::PAID_STATUS['Paid']);
    }

    public function unpaidInvitationPackages()
    {
        return $this->hasMany(InvitationPackage::class)->where('status', Constant::PAID_STATUS["Not Paid"]);
    }

    public function pendingInvitationPackages()
    {
        return $this->hasMany(InvitationPackage::class)->where('status', Constant::PAID_STATUS["Pending Admin Payment"]);
    }


    public function currentPackage()
    {
//        return $this->hasOne(InvitationPackage::class)->where('status', Constant::PAID_STATUS['Pending Admin Payment']);
        return $this->hasOne(InvitationPackage::class);

    }

    public function packages()
    {
        return $this->hasManyThrough(
            \App\Models\Package::class,
            \App\Models\InvitationPackage::class,
            'invitation_id', // FK on invitation_packages
            'id',            // PK on packages
            'id',            // PK on invitations
            'package_id'     // FK on invitation_packages

        );
    }

    public function totalInvitationsCount() 
    {
        // Fixed: Use $this->id instead of $invitation->id
        // Use relationship instead of direct query for better performance
        $extraCountInvitation = $this->paidInvitationPackages()->sum('count') ?? 0;

        $invitationCount = checkPackageCount($this, 'checkAllUsersInvitationCount');

        return $extraCountInvitation + $invitationCount;
    }

    public function totalUnPaidInvitationsCount() 
    {
        // Fixed: Use $this->id instead of $invitation->id
        // Use relationship instead of direct query for better performance
        $extraCountInvitation = $this->invitationPackages()->sum('count') ?? 0;

        $invitationCount = checkPackageCount($this, 'checkAllUsersInvitationCount');

        return $extraCountInvitation + $invitationCount;
    }

    // Alternative: Using Eloquent attribute accessor
    public function getTotalInvitationsCountAttribute()
    {
        return $this->totalInvitationsCount();
    }

    // Method to get extra invitation count using relationship
    public function getExtraInvitationCountAttribute()
    {
        return $this->paidInvitationPackages()->sum('count') ?? 0;
    }

    // Method to get remaining invitation count
    public function getRemainingInvitationsAttribute()
    {
        $totalAvailable = $this->totalInvitationsCount();
        $usedCount = $this->users()->sum('invitation_user.invitation_count');
        return max(0, $totalAvailable - $usedCount);
    }

    /**
     * Get invitation status based on date, time and 8-hour buffer
     */
    public function getInvitationStatus()
    {
        // If no date is set, return the original status
        if (!$this->date) {
            return $this->status;
        }

        // Combine date and time to create a full datetime
        $invitationDateTime = $this->time 
            ? Carbon::parse($this->date . ' ' . $this->time)
            : Carbon::parse($this->date)->endOfDay(); // If no time, assume end of day

        // Add 8 hours to the invitation datetime
        $finishDateTime = $invitationDateTime->copy()->addHours(8);

        // Check if current time is after invitation time + 8 hours
        if (Carbon::now()->greaterThan($finishDateTime)) {
            return Constant::INVITATION_STATUS['Finished Invitation'];
        }

        // Return the original status if not finished
        return $this->status;
    }


}
