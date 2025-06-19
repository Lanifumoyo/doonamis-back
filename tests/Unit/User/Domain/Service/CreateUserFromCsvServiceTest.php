<?php

namespace Tests\Unit\User\Domain\Service;

use App\Models\User;
use Doonamis\User\Domain\Repository\UserRepository;
use Doonamis\User\Domain\Service\CreateUserFromCsvService;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class CreateUserFromCsvServiceTest extends TestCase
{
    private MockObject&UserRepository $userRepository;
    private CreateUserFromCsvService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->service = new CreateUserFromCsvService($this->userRepository);        
    }

    public function test_creates_user_with_hashed_password(): void
    {
        $data = [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'last_name' => 'Test Last Name',
            'password' => 'plaintext_password',
            'address' => 'Test Address',
            'phone' => '1234567890'
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
                
                return true;
            }));

        $this->service->sync($data);
    }

    public function test_creates_user_with_minimal_data(): void
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
                
                $this->assertNotEquals(
                    Hash::make($data['password']),
                    $user->password
                );
                
                return true;
            }));

        $this->service->sync($data);
    }
} 