<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\ProjectManagement\Project;
use App\Models\ProjectManagement\Comment;
use App\Models\Role;
use App\Models\Users\Department;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

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
    return $this->belongsToMany(Project::class);
}

// En el modelo User
public function departments()
{
    return $this->belongsToMany(Department::class, 'user_department' );
}

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    
}
