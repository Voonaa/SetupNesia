<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    /**
     * Get all users who are customers.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllCustomers(): Collection
    {
        return User::where('role', 'customer')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Find user by ID.
     *
     * @param int $id
     * @return \App\Models\User
     */
    public function getUserById(int $id): User
    {
        return User::findOrFail($id);
    }

    /**
     * Update user details and role safely.
     *
     * @param \App\Models\User $user
     * @param array $data
     * @return \App\Models\User
     * @throws \Exception
     */
    public function updateUser(User $user, array $data): User
    {
        // Don't let users edit the role of the primary admin or delete them
        if ($user->email === 'admin@setupnesia.com' && isset($data['role']) && $data['role'] !== 'admin') {
            throw new \Exception("Cannot demote the primary administrator account.");
        }

        $user->update($data);
        return $user;
    }

    /**
     * Delete a customer safely.
     *
     * @param \App\Models\User $user
     * @return bool
     * @throws \Exception
     */
    public function deleteUser(User $user): bool
    {
        // Prevent deleting the primary admin account
        if ($user->email === 'admin@setupnesia.com') {
            throw new \Exception("Cannot delete the primary administrator account.");
        }

        // Restrict delete if the user has historical orders (relational restrict)
        if ($user->orders()->count() > 0) {
            throw new \Exception("Cannot delete user because they have active or completed orders.");
        }

        // Deleting user will cascade delete their cart if they have one
        return $user->delete();
    }
}
