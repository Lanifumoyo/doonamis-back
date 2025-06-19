<?php

namespace Doonamis\User\Application\Handler;

use Doonamis\User\Application\Command\UploadFromCsvCommand;
use Doonamis\User\Domain\Repository\UserRepository;
use Doonamis\User\Domain\Service\GetUserDataFromCsvService;
use Doonamis\User\Domain\Service\ImportUserCsvFormatValidator;
use Doonamis\User\Domain\Service\SyncUserFromCsvFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class UploadFromCsvCommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private SyncUserFromCsvFactory $syncUserFromCsvFactory,
        private ImportUserCsvFormatValidator $importUserCsvFormatValidator,
        private GetUserDataFromCsvService $getRecordsFromCsvService
    ) {}

    public function handle(UploadFromCsvCommand $command)
    {
        $this->validateCsvType($command->file);
        
        $records = $this->getRecordsFromCsvService->execute($command->file->getRealPath());

        foreach ($records as $record) {
            $this->importUserCsvFormatValidator->validate($record);
            
            $user = $this->userRepository->findByEmail(trim($record['email']));
            
            $syncUserFromCsvService = $this->syncUserFromCsvFactory->create($user);

            $syncUserFromCsvService->sync($record);
        }
    }

    private function validateCsvType(UploadedFile $file): void
    {
        if($file->getClientOriginalExtension() !== 'csv') {
            throw new InvalidArgumentException(
                'Solamente se aceptan archivos CSV',
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }
}