<?php

namespace Doonamis\User\Domain\Service;

use App\Models\User;
use Doonamis\User\Domain\Repository\UserRepository;
use Illuminate\Support\Facades\Hash;

class CreateUserFromCsvService extends BaseImportUserCsvService
{
    private const PASSWORD_FIELD_NAME = 'password';
    
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function sync(array $data): void
    {
        $userData = $this->hashPassword($data);

        $user = new User();

        $user->fill($userData);

        $this->userRepository->save($user);
    }
}