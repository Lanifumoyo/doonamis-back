<?php

namespace Tests\Unit\User\Domain\Service;

use Doonamis\User\Domain\Service\ImportUserCsvFormatValidator;
use InvalidArgumentException;
use Tests\TestCase;

class ImportUserCsvFormatValidatorTest extends TestCase
{
    private ImportUserCsvFormatValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new ImportUserCsvFormatValidator();
    }

    public function test_validates_record_with_all_required_fields(): void
    {
        $record = [
            'address' => 'Test Address',
            'email' => 'test@example.com',
            'last_name' => 'Test Last Name',
            'name' => 'Test User',
            'password' => 'password123',
            'phone' => '1234567890'
        ];

        $this->expectNotToPerformAssertions();
        $this->validator->validate($record);
    }

    public function test_throws_exception_when_missing_required_fields(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El archivo CSV debe tener las siguientes columnas: address, email, last_name, name, password, phone');

        $record = [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'password' => 'password123'
        ];

        $this->validator->validate($record);
    }

    public function test_throws_exception_when_missing_specific_fields(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El archivo CSV debe tener las siguientes columnas: address, email, last_name, name, password, phone');

        $record = [
            'address' => 'Test Address',
            'email' => 'test@example.com',
            'name' => 'Test User',
            'password' => 'password123',
            'phone' => '1234567890'
        ];

        $this->validator->validate($record);
    }

    public function test_throws_exception_when_email_is_null(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El archivo CSV no puede tener los siguientes campos nulos: email, name, password');

        $record = [
            'address' => 'Test Address',
            'email' => null,
            'last_name' => 'Test Last Name',
            'name' => 'Test User',
            'password' => 'password123',
            'phone' => '1234567890'
        ];

        $this->validator->validate($record);
    }

    public function test_throws_exception_when_name_is_null(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El archivo CSV no puede tener los siguientes campos nulos: email, name, password');

        $record = [
            'address' => 'Test Address',
            'email' => 'test@example.com',
            'last_name' => 'Test Last Name',
            'name' => null,
            'password' => 'password123',
            'phone' => '1234567890'
        ];

        $this->validator->validate($record);
    }

    public function test_throws_exception_when_password_is_null(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El archivo CSV no puede tener los siguientes campos nulos: email, name, password');

        $record = [
            'address' => 'Test Address',
            'email' => 'test@example.com',
            'last_name' => 'Test Last Name',
            'name' => 'Test User',
            'password' => null,
            'phone' => '1234567890'
        ];

        $this->validator->validate($record);
    }

    public function test_throws_exception_when_multiple_essential_fields_are_null(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El archivo CSV no puede tener los siguientes campos nulos: email, name, password');

        $record = [
            'address' => 'Test Address',
            'email' => null,
            'last_name' => 'Test Last Name',
            'name' => null,
            'password' => null,
            'phone' => '1234567890'
        ];

        $this->validator->validate($record);
    }

    public function test_validates_record_when_optional_fields_are_null(): void
    {
        $record = [
            'address' => null,
            'email' => 'test@example.com',
            'last_name' => null,
            'name' => 'Test User',
            'password' => 'password123',
            'phone' => null
        ];

        $this->expectNotToPerformAssertions();
        $this->validator->validate($record);
    }
} 