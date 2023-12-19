<?php

namespace Tests\Feature\Auth;

use App\Enums\Status;
use App\Mail\Registration\SendEmailAddressVerificationMail;
use App\Mail\Registration\SendRegistrationSuccessfulMail;
use App\Models\User;
use App\Services\RegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class RegistrationProcessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Send registration request
     */
    private function registrationRequest(array $userData): TestResponse
    {
        return $this->post('/api/registration', $userData, ['Accept' => 'application/json']);
    }

    /**
     * Registration performed successfully
     */
    public function testRegistrationSuccessful(): void
    {
        $password                          = 'password';
        $userData                          = User::factory()->make()->toArray();
        $userData['password']              = $password;
        $userData['password_confirmation'] = $password;

        $response = $this->registrationRequest($userData);

        $response->assertCreated();
        $user = User::findOrFail(1);
        $this->assertSame($user->first_name, $userData['first_name']);
        $this->assertTrue(Hash::check($password, $user->password));
    }

    /**
     * Registration failed due to validation
     * @dataProvider dataProviderForTestRegistrationFailedDueToValidation
     */
    public function testRegistrationFailedDueToValidation($userData): void
    {
        $response = $this->registrationRequest($userData);

        $response->assertUnprocessable();
    }

    public static function dataProviderForTestRegistrationFailedDueToValidation(): array
    {
        return [
            [
                [
                    'first_name'            => 'Karim',
                    'date_of_birth'         => '23/07/1991',
                    'email'                 => 'email@gmail.com',
                    'password'              => 'password',
                    'password_confirmation' => 'password',
                ],
            ],
            [
                [
                    'first_name'            => 'Karim',
                    'last_name'             => 'Khan',
                    'email'                 => 'email@gmail.com',
                    'password'              => 'password',
                    'password_confirmation' => 'password',
                ],
            ],
        ];
    }

    /**
     * Check unique email
     */
    public function testEmailAlreadyExist(): void
    {
        $user = User::factory()->create();

        $password                          = 'password';
        $userData                          = User::factory()->make()->toArray();
        $userData['email']                 = $user->email;
        $userData['password']              = $password;
        $userData['password_confirmation'] = $password;
        $response                          = $this->registrationRequest($userData);

        $response->assertUnprocessable();
    }

    /**
     * Test after registration email has been sent for verification
     */
    public function testEmailVerificationEmailOnQueue()
    {
        Mail::fake();
        $password                          = 'password';
        $userData                          = User::factory()->make()->toArray();
        $userData['password']              = $password;
        $userData['password_confirmation'] = $password;

        $data = $this->registrationRequest($userData);
        /**
         * @var $user User
         */
        $user = json_decode($data->getContent())->data;

        Mail::assertQueued(SendEmailAddressVerificationMail::class, function (SendEmailAddressVerificationMail $mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /**
     * Test email verification email template
     */
    public function testEmailVerificationMailContent()
    {
        /**
         * @var $user User
         */
        $user     = User::factory()->create();
        $mailable = new SendEmailAddressVerificationMail($user);
        $mailable->assertHasSubject('Welcome to ADIEU - Confirm Your Email Address');
        $mailable->assertSeeInHtml($user->getRememberToken());
        $mailable->assertSeeInHtml($user->getFullName());
    }

    /**
     * Test email verification is working fine
     */
    public function testEmailVerificationUrlWorkingProperly()
    {
        /**
         * @var $user User
         */
        $user = User::factory()->unverified()->create();
        Mail::fake();
        $response = $this->get((new RegistrationService())->emailVerificationUrl($user));

        $response->assertSuccessful();
        $dbUser = User::findOrFail($user->id);
        $this->assertSame(Status::from($dbUser->status), Status::Active);
        $this->assertNotNull($dbUser->email_verified_at);

        Mail::assertQueued(SendRegistrationSuccessfulMail::class, function (SendRegistrationSuccessfulMail $mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /**
     * Test email verification with wrong token
     */
    public function testEmailVerificationUrlWithWrongToken()
    {
        /**
         * @var $user User
         */
        $user = User::factory()->create();
        $user->remember_token = Str::random(10);

        $response = $this->get((new RegistrationService())->emailVerificationUrl($user));

        $response->assertNotFound();
    }

    /**
     * Test registration successful mail template
     */
    public function testRegistrationSuccessfulMailContent()
    {
        $registrationService = new RegistrationService();
        /**
         * @var $user User
         */
        $user     = User::factory()->create();
        $mailable = new SendRegistrationSuccessfulMail($user);
        $mailable->assertHasSubject('Welcome to ADIEU - Your Account is Activated!');
        $mailable->assertSeeInHtml($registrationService->loginUrl());
        $mailable->assertSeeInHtml($registrationService->supportEmail());
    }
}
