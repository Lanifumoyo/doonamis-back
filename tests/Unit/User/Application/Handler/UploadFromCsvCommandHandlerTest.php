<?php

namespace Tests\Unit\User\Application\Handler;

use App\Models\User;
use Doonamis\User\Application\Command\UploadFromCsvCommand;
use Doonamis\User\Application\Handler\UploadFromCsvCommandHandler;
use Doonamis\User\Domain\Repository\UserRepository;
use Doonamis\User\Domain\Service\CreateUserFromCsvService;
use Doonamis\User\Domain\Service\GetUserDataFromCsvService;
use Doonamis\User\Domain\Service\ImportUserCsvFormatValidator;
use Doonamis\User\Domain\Service\SyncUserFromCsvFactory;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class UploadFromCsvCommandHandlerTest extends TestCase
{
    private MockObject&UserRepository $userRepository;
    private MockObject&SyncUserFromCsvFactory $syncUserFromCsvFactory;
    private MockObject&ImportUserCsvFormatValidator $importUserCsvFormatValidator;
    private MockObject&GetUserDataFromCsvService $getRecordsFromCsvService;

    private UploadFromCsvCommandHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->userRepository =$this->createMock(UserRepository::class);
        $this->syncUserFromCsvFactory =$this->createMock(SyncUserFromCsvFactory::class);
        $this->importUserCsvFormatValidator =$this->createMock(ImportUserCsvFormatValidator::class);
        $this->getRecordsFromCsvService =$this->createMock(GetUserDataFromCsvService::class);
        
        $this->handler = new UploadFromCsvCommandHandler(
            $this->userRepository,
            $this->syncUserFromCsvFactory,
            $this->importUserCsvFormatValidator,
            $this->getRecordsFromCsvService
        );
    }

    public function test_throws_exception_when_file_is_not_csv(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Solamente se aceptan archivos CSV');

        $file =$this->createMock(UploadedFile::class);
        $file->expects($this->once())
            ->method('getClientOriginalExtension')
            ->willReturn('txt');

        $command = new UploadFromCsvCommand($file);
        $this->handler->handle($command);
    }

    public function test_validate_users_are_updated(): void
    {
        $file =$this->createMock(UploadedFile::class);
        $file->expects($this->once())
            ->method('getClientOriginalExtension')
            ->willReturn('csv');

        $records = [
            [
                'email' => 'test@example.com',
                'name' => 'Test User',
                'password' => 'password123'
            ]
        ];

        $this->getRecordsFromCsvService
            ->expects($this->once())
            ->method('execute')
            ->willReturn(new \ArrayIterator($records));

        $this->importUserCsvFormatValidator
            ->expects($this->once())
            ->method('validate')
            ->with($records[0]);

        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with('test@example.com')
            ->willReturn(null);

        $createUserFromCsvService =$this->createMock(CreateUserFromCsvService::class);
        $createUserFromCsvService->expects($this->once())
            ->method('sync')
            ->with($records[0]);

        $this->syncUserFromCsvFactory
            ->expects($this->once())
            ->method('create')
            ->with(null)
            ->willReturn($createUserFromCsvService);

        $command = new UploadFromCsvCommand($file);
        $this->handler->handle($command);
    }
} 