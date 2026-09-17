<?php

namespace App\Models;

use App\Traits\HasActivityLogs;
use Illuminate\Database\Eloquent\Model;

class OrganizationSocialProfiles extends Model
{

    use HasActivityLogs;

    protected $table = 'organization_social_profiles';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'platform',
        'username',
        'url',
    ];

    // FK relationships untuk social media profiles organisasi
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }
}
