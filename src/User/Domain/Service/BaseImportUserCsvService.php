<?php

namespace Doonamis\User\Domain\Service;

use Illuminate\Support\Facades\Hash;

abstract class BaseImportUserCsvService implements SyncUserFromCsvService
{
    private const PASSWORD_FIELD_NAME = 'password';

    protected function hashPassword(array $data): array
    {
        $data[self::PASSWORD_FIELD_NAME] = Hash::make($data[self::PASSWORD_FIELD_NAME]);

        return $data;
    }
}