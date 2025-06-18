<?php

namespace Doonamis\User\Application\Handler;

use Doonamis\User\Application\Command\DeleteUserCommand;
use Doonamis\User\Domain\Repository\UserRepository;

class DeleteUserCommandHandler
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function handle(DeleteUserCommand $command)
    {
        $this->userRepository->delete($command->userIdToDelete);
    }
}