<?php

namespace Doonamis\User\Domain\Service;

use App\Models\User;
use Doonamis\User\Domain\Repository\UserRepository;

class UpdateUserFromCsvService extends BaseImportUserCsvService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly User $user
    ) {}

    public function sync(array $data): void
    {
        $userData = $this->hashPassword($data);
        $userData['deleted_at'] = null;
        
        $this->user->fill($userData);

        $this->userRepository->save($this->user);
    }
}