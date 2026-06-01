<?php

namespace App\Services;

use App\Models\User;

class RedirectService
{
    /**
     * Get the redirect URL for a user based on their role.
     *
     * @param  \App\Models\User  $user
     * @return string
     */
    public function getRedirectUrlForUser(User $user): string
    {
        if ($user->isAdmin()) {
            return route('admin.dashboard');
        }

        return route('dashboard');
    }
}
