<?php

namespace Doonamis\User\Application\Command;

class ListUsersCommand
{
    public function __construct(
        public readonly int $actingUserId
    ) {}
}