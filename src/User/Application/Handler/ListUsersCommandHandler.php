<?php

namespace Doonamis\User\Application\Handler;

use Doonamis\User\Application\Command\ListUsersCommand;
use Doonamis\User\Domain\Repository\UserRepository;
use Illuminate\Database\Eloquent\Collection;

class ListUsersCommandHandler
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function handle(ListUsersCommand $command): Collection
    {
        return $this->userRepository->findActiveUsers($command->actingUserId);
    }
}