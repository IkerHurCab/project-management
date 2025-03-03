<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Users\Department;



class Organization extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $table = 'organization';

    protected $fillable = [
        'name',
        'description',
        'organization_head',
        'organization_logo',
        'organization_banner'
    ];

    /**
     * Get the user that is the head of the organization.
     */
    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organization_head');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class, 'organization_id');
    }
    
}
