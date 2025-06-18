<?php

namespace Doonamis\User\Infrastructure\Repository;

use App\Models\User;
use Doonamis\User\Domain\Repository\UserRepository;
use Illuminate\Database\Eloquent\Collection;

class EloquentUserRepository implements UserRepository
{
    public function findActiveUsers(int $actingUserId): Collection
    {
        return User::where('deleted_at', null)
            ->where('id', '!=', $actingUserId)
            ->get();
    }

    public function delete(int $userIdToDelete): void
    {
        User::where('id', $userIdToDelete)->update(['deleted_at' => now()]);
    }

    public function save(User $user): void
    {
        $user->save();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}