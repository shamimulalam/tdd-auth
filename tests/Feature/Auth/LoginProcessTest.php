<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class LoginProcessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Send registration request
     */
    private function loginRequest(array $credential): TestResponse
    {
        return $this->post('/api/login', $credential, ['Accept' => 'application/json']);
    }

    /**
     * Test successful login attempt
     */
    public function testSuccessfulLogin(): void
    {
        $password   = '12345678';
        $user       = User::factory(['password' => $password])->create();
        $credential = [
            'email'    => $user->email,
            'password' => $password,
        ];

        $response = $this->loginRequest($credential);

        $response->assertStatus(200);

        /**
         * @var $responseUser User
         */
        $responseUser = json_decode($response->getContent())->data;
        $this->assertSame($user->email, $responseUser->email);
    }

    /**
     * Test unsuccessful login attempt with wrong email
     */
    public function testUnsuccessfulLoginWithWrongEmail(): void
    {
        $email      = 'a@a.a';
        $wrongEmail = 'b@b.b';
        $user       = User::factory(['email' => $email])->create();
        $credential = [
            'email'    => $wrongEmail,
            'password' => 'password',
        ];

        $response = $this->loginRequest($credential);

        $response->assertUnauthorized();
        $message = json_decode($response->getContent())->message;
        $this->assertSame($message, 'Wrong email address');
    }

    /**
     * Test unsuccessful login attempt with wrong password
     */
    public function testUnsuccessfulLoginWithWrongPassword(): void
    {
        $user       = User::factory(['password' => 'password'])->create();
        $credential = [
            'email'    => $user->email,
            'password' => '12345678',
        ];

        $response = $this->loginRequest($credential);

        $response->assertUnauthorized();
        $message = json_decode($response->getContent())->message;
        $this->assertSame($message, 'Wrong password');
    }
}
