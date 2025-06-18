<?php

namespace Doonamis\User\Domain\Service;

use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class ImportUserCsvFormatValidator
{
    private const REQUIRED_FIELDS = [
        'address',
        'email',
        'last_name',
        'name',
        'password',
        'phone',
    ];

    private const NONE_NULLABLE_FIELDS = [
        'email',
        'name',
        'password',
    ];

    public function validate(array $record): void
    {
        $missingFields = array_diff(self::REQUIRED_FIELDS, array_keys($record));

        if(count($missingFields) > 0) {
            throw new InvalidArgumentException(
                'El archivo CSV debe tener las siguientes columnas: ' . implode(', ', self::REQUIRED_FIELDS),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        if(!$this->hasEssentialData($record)) {
            throw new InvalidArgumentException(
                'El archivo CSV no puede tener los siguientes campos nulos: ' . implode(', ', self::NONE_NULLABLE_FIELDS),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }

    private function hasEssentialData(array $record): bool
    {
        foreach(self::NONE_NULLABLE_FIELDS as $field) {
            if(is_null($record[$field])) {
                return false;
            }
        }
        return true;
    }
}