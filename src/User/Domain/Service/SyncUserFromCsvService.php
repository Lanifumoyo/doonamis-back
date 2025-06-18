<?php

namespace Doonamis\User\Domain\Service;

interface SyncUserFromCsvService
{
    public function sync(array $data): void;
}