<?php

namespace Doonamis\User\Domain\Repository;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepository
{
    public function findActiveUsers(int $actingUserId): Collection;

    public function delete(int $userIdToDelete): void;

    public function save(User $user): void;

    public function findByEmail(string $email): ?User;
}