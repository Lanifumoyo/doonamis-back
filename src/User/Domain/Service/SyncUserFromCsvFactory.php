<?php 

namespace Doonamis\User\Domain\Service;

use App\Models\User;
use Doonamis\User\Domain\Repository\UserRepository;

class SyncUserFromCsvFactory
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function create(?User $user): SyncUserFromCsvService
    {   
        if(is_null($user)) {
            return new CreateUserFromCsvService($this->userRepository);
        }
        return new UpdateUserFromCsvService($this->userRepository, $user);
    }
}