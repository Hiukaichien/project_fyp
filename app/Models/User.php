<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'superadmin', // Added superadmin field
        'can_be_deleted', // Added can_be_deleted field
        'visible_projects', // Added visible_projects field
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'superadmin', // Hide superadmin field from serialization
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
            'visible_projects' => 'array',
        ];
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the projects that belong to the user.
     */
    public function projects()
    {
        return $this->hasMany(\App\Models\Project::class);
    }

    /**
     * Get the projects that are visible to the user.
     * If visible_projects is null, user can see all projects.
     * If it's an array, user can only see projects with those IDs.
     */
    public function visibleProjects()
    {
        if (is_null($this->visible_projects)) {
            // User can see all projects
            return \App\Models\Project::query();
        }
        
        // User can only see specific projects
        return \App\Models\Project::whereIn('id', $this->visible_projects);
    }

    /**
     * Check if user can see a specific project.
     */
    public function canSeeProject($projectId)
    {
        // Admins can see all projects
        if ($this->superadmin === 'yes') {
            return true;
        }
        
        // If visible_projects is null, user can see all projects
        if (is_null($this->visible_projects)) {
            return true;
        }
        
        // User can only see specific projects
        return in_array($projectId, $this->visible_projects);
    }

    /**
     * Get the default visible projects for this user (their owned projects).
     */
    public function getDefaultVisibleProjects()
    {
        return $this->projects()->pluck('id')->toArray();
    }

    /**
     * Initialize visible_projects if it's null.
     */
    public function initializeVisibleProjects()
    {
        if (is_null($this->visible_projects)) {
            $this->visible_projects = $this->getDefaultVisibleProjects();
            return true; // Indicates that initialization happened
        }
        return false; // No initialization needed
    }
}
