<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use App\Models\Project;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
   public function boot(): void
    {
        // This gate checks if the authenticated user can access the project.
        Gate::define('access-project', function (User $user, Project $project) {
            if ($user->superadmin === 'yes') {
                return true; // Superadmins can access all projects
            }
            
            if ($user->id === $project->user_id) {
                return true; // Users can always access their own projects
            }
            
            // Check if project is in user's visible projects list
            if (is_null($user->visible_projects)) {
                return true; // User can access all projects (legacy behavior)
            }
            
            return in_array($project->id, $user->visible_projects);
        });
    }
}