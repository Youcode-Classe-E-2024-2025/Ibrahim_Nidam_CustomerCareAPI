<?php

namespace Tests\Unit\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\NewAccessToken;
use Mockery;
use PHPUnit\Framework\TestCase;

class AuthServiceTest extends TestCase
{
    protected $userRepository;
    protected $authService;
    protected $user;
    protected $accessToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->authService = new AuthService($this->userRepository);

        // Create mock user
        $this->user = Mockery::mock(User::class);
        $this->user->shouldReceive('createToken')
            ->andReturnUsing(function ($name) {
                $this->accessToken = Mockery::mock(NewAccessToken::class);
                $this->accessToken->plainTextToken = 'mock-token-string';
                return $this->accessToken;
            });
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_registers_a_regular_user_successfully()
    {
        // Arrange
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $expectedUserData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123', // This remains in the actual code
            'role' => 'user'
        ];

        // Expectations
        $this->userRepository->shouldReceive('createUser')
            ->once()
            ->with(Mockery::on(function ($arg) use ($expectedUserData) {
                return $arg['name'] === $expectedUserData['name'] &&
                       $arg['email'] === $expectedUserData['email'] &&
                       $arg['password'] === $expectedUserData['password'] &&
                       $arg['role'] === $expectedUserData['role'];
            }))
            ->andReturn($this->user);

        // Act
        $result = $this->authService->register($userData);

        // Assert
        $this->assertSame($this->user, $result['user']);
        $this->assertEquals('mock-token-string', $result['token']);
    }

    /** @test */
    public function it_registers_an_agent_with_valid_agent_code()
    {
        // Arrange
        $userData = [
            'name' => 'Agent Smith',
            'email' => 'agent@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agent_code' => 'VALID_AGENT_CODE'
        ];

        $expectedUserData = [
            'name' => 'Agent Smith',
            'email' => 'agent@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123', // This remains in the actual code
            'role' => 'agent'
        ];

        // Expectations
        $this->userRepository->shouldReceive('validateAgentCode')
            ->once()
            ->with('VALID_AGENT_CODE')
            ->andReturn(true);

        $this->userRepository->shouldReceive('createUser')
            ->once()
            ->with(Mockery::on(function ($arg) use ($expectedUserData) {
                return $arg['name'] === $expectedUserData['name'] &&
                       $arg['email'] === $expectedUserData['email'] &&
                       $arg['password'] === $expectedUserData['password'] &&
                       $arg['role'] === $expectedUserData['role'];
            }))
            ->andReturn($this->user);

        // Act
        $result = $this->authService->register($userData);

        // Assert
        $this->assertSame($this->user, $result['user']);
        $this->assertEquals('mock-token-string', $result['token']);
    }

    /** @test */
    public function it_registers_user_with_invalid_agent_code()
    {
        // Arrange
        $userData = [
            'name' => 'Failed Agent',
            'email' => 'failed@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agent_code' => 'INVALID_AGENT_CODE'
        ];

        $expectedUserData = [
            'name' => 'Failed Agent',
            'email' => 'failed@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123', // This remains in the actual code
            'role' => 'user'
        ];

        // Expectations
        $this->userRepository->shouldReceive('validateAgentCode')
            ->once()
            ->with('INVALID_AGENT_CODE')
            ->andReturn(false);

        $this->userRepository->shouldReceive('createUser')
            ->once()
            ->with(Mockery::on(function ($arg) use ($expectedUserData) {
                return $arg['name'] === $expectedUserData['name'] &&
                       $arg['email'] === $expectedUserData['email'] &&
                       $arg['password'] === $expectedUserData['password'] &&
                       $arg['role'] === $expectedUserData['role'];
            }))
            ->andReturn($this->user);

        // Act
        $result = $this->authService->register($userData);

        // Assert
        $this->assertSame($this->user, $result['user']);
        $this->assertEquals('mock-token-string', $result['token']);
    }


    /** @test */
    public function it_fails_to_login_with_invalid_email()
    {
        // Arrange
        $credentials = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123'
        ];

        // Expectations
        $this->userRepository->shouldReceive('findUserByEmail')
            ->once()
            ->with('nonexistent@example.com')
            ->andReturn(null);

        // Act
        $result = $this->authService->login($credentials);

        // Assert
        $this->assertNull($result);
    }



    /** @test */
    public function it_successfully_logs_out_a_user()
    {
        // Arrange
        $tokenCollection = Mockery::mock('Illuminate\Database\Eloquent\Collection');
        $tokenCollection->shouldReceive('delete')->once()->andReturn(true);

        $user = Mockery::mock(User::class);
        $user->shouldReceive('tokens')->once()->andReturn($tokenCollection);

        // Act
        $result = $this->authService->logout($user);

        // Assert
        $this->assertTrue($result);
    }

    /** @test */
    public function it_fails_to_login_with_invalid_password()
    {
        // Arrange
        $credentials = [
            'email' => 'user@example.com',
            'password' => 'wrong_password'
        ];

        // Create a proper mock for User model with necessary attributes
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('password')->andReturn('hashed_password');
        $user->shouldReceive('setAttribute')->andReturn(true); // Add this line

        // Expectations
        $this->userRepository->shouldReceive('findUserByEmail')
            ->once()
            ->with('user@example.com')
            ->andReturn($user);

        Hash::shouldReceive('check')
            ->once()
            ->with('wrong_password', 'hashed_password')
            ->andReturn(false);

        // Act
        $result = $this->authService->login($credentials);

        // Assert
        $this->assertNull($result);
    }
}