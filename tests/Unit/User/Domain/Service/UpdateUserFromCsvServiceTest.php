<?php

namespace Tests\Unit\User\Domain\Service;

use App\Models\User;
use Doonamis\User\Domain\Repository\UserRepository;
use Doonamis\User\Domain\Service\UpdateUserFromCsvService;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class UpdateUserFromCsvServiceTest extends TestCase
{
    private MockObject&UserRepository $userRepository;
    private User $user;
    private UpdateUserFromCsvService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->user = new User();
        $this->service = new UpdateUserFromCsvService($this->userRepository, $this->user);
    }

    public function test_updates_user_with_hashed_password(): void
    {
        $data = [
            'email' => 'updated@example.com',
            'name' => 'Updated User',
            'last_name' => 'Updated Last Name',
            'password' => 'new_password',
            'address' => 'Updated Address',
            'phone' => '9876543210'
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (User $user) use ($data) {
                $this->assertEquals($data['email'], $user->email);
                $this->assertEquals($data['name'], $user->name);
                $this->assertEquals($data['last_name'], $user->last_name);
                $this->assertEquals($data['address'], $user->address);
                $this->assertEquals($data['phone'], $user->phone);
                $this->assertNotEquals($data['password'], $user->password);
                $this->assertTrue(Hash::check($data['password'], $user->password));
                $this->assertNull($user->deleted_at);
                
                return true;
            }));

        $this->service->sync($data);
    }

    public function test_updates_user_with_minimal_data(): void
    {
        $data = [
            'email' => 'minimal@example.com',
            'name' => 'Minimal User',
            'last_name' => null,
            'password' => 'simple_password',
            'address' => null,
            'phone' => null
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (User $user) use ($data) {
                $this->assertEquals($data['email'], $user->email);
                $this->assertEquals($data['name'], $user->name);
                $this->assertNull($user->last_name);
                $this->assertNull($user->address);
                $this->assertNull($user->phone);
                $this->assertTrue(Hash::check($data['password'], $user->password));
                $this->assertNull($user->deleted_at);
                
                return true;
            }));

        $this->service->sync($data);
    }

    public function test_updates_user_and_restores_deleted_user(): void
    {
        $this->user->deleted_at = now();

        $data = [
            'email' => 'restored@example.com',
            'name' => 'Restored User',
            'last_name' => 'Restored Last Name',
            'password' => 'restored_password',
            'address' => 'Restored Address',
            'phone' => '5555555555'
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (User $user) use ($data) {
                $this->assertEquals($data['email'], $user->email);
                $this->assertEquals($data['name'], $user->name);
                $this->assertEquals($data['last_name'], $user->last_name);
                $this->assertEquals($data['address'], $user->address);
                $this->assertEquals($data['phone'], $user->phone);
                $this->assertTrue(Hash::check($data['password'], $user->password));
                $this->assertNull($user->deleted_at);
                
                return true;
            }));

        $this->service->sync($data);
    }

    public function test_password_is_properly_hashed(): void
    {
        $data = [
            'email' => 'hash@example.com',
            'name' => 'Hash User',
            'last_name' => 'Hash Last Name',
            'password' => 'very_secure_password_123!@#',
            'address' => 'Hash Address',
            'phone' => '1111111111'
        ];

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (User $user) use ($data) {
                $this->assertNotEquals($data['password'], $user->password);
                $this->assertTrue(Hash::check($data['password'], $user->password));
                $this->assertNull($user->deleted_at);
                
                return true;
            }));

        $this->service->sync($data);
    }
} 