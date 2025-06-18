<?php

namespace Doonamis\User\Application\Command;

class DeleteUserCommand
{
    public function __construct(
        public readonly int $userIdToDelete
    ) {}
}