<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Organization extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'organization_head',
        'organization_logo',
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
}
