<?php

namespace App\Models;

use App\Traits\AutoTranslatableAttributes;
use App\Traits\ClearsHomeCache;
use App\Traits\HasHumanCreatedAt;
use App\Traits\NotifiesAdminsForApproval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Illuminate\Support\Facades\Storage;


class Video extends Model
{
    use BelongsToTenant, SoftDeletes, HasTranslations, AutoTranslatableAttributes, HasHumanCreatedAt,ClearsHomeCache, NotifiesAdminsForApproval;
    protected $fillable = [
        'description',
        'video_path',
        'type',
        'is_active',
        'is_admin_approved',
        'publish_date',
        'user_id',
    ];
    public $translatable = ['description'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_admin_approved' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getVideoPathAttribute($value)
    {
        if(str_contains($value, 'videos')){
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('spaces');
            return $value ? $disk->url($value) : null;
        }else{
            return $value;
        }
    }
    public function scopeForPublic($query)
    {
        return $query->where('is_active', true)
            ->where('is_admin_approved', true)
            ->where('publish_date', '<=', now())
            ->orderBy('created_at', 'desc');
    }
}
