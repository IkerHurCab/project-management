<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Users\Organization;
use App\Models\ProjectManagement\Project;
use App\Models\ProjectManagement\Comment;
use App\Models\Role;
use App\Models\Users\Department;
use App\Models\ProjectManagement\Task;
use App\Models\Notification;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'status',
        'password',
        'two_factor_secret', 
        'two_factor_recovery_codes', 
        'two_factor_confirmed_at'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }
    
    public function hasRole($role)
    {
        return $this->roles->pluck('name')->contains($role);
    }
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user', 'user_id', 'project_id');
    }
    

    public function organizations() {
        return $this->belongsToMany(Organization::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

public function departments()
{
    return $this->belongsToMany(Department::class, 'user_department' );
}

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function currentOrganization()
    {
        return $this->belongsToMany(Organization::class, 'organization_user')
                    ->wherePivot('is_current', 't');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
    

    
}
