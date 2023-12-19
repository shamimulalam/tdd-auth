<?php

namespace App\Services;

use App\Enums\Status;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Mail\Registration\SendEmailAddressVerificationMail;
use App\Mail\Registration\SendRegistrationSuccessfulMail;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RegistrationService extends CoreService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function processRegistration(RegistrationRequest $request): User
    {
        $data = [
            'first_name'     => $request['first_name'],
            'last_name'      => $request['last_name'],
            'date_of_birth'  => $request['date_of_birth'],
            'email'          => $request['email'],
            'password'       => Hash::make($request['password']),
            'remember_token' => Str::uuid()->toString(),
        ];

        /**
         * @var $user User
         */
        $user = $this->userRepository->createNewUser($data);

        Mail::to($user->email)->send(new SendEmailAddressVerificationMail($user));

        return $user;
    }

    public function processConfirmUrlRequest(Request $request): User
    {
        $user = $this->userRepository->getUserByRememberTokenAndStatusPending($request->get('token'));

        if (!$user) {
            throw new NotFoundHttpException('Resource not found');
        }

        // @todo This code should move to repository
        $user->status            = Status::Active;
        $user->email_verified_at = now();
        $user->save();

        Mail::to($user->email)->send(new SendRegistrationSuccessfulMail($user));

        return $user;
    }

    public function emailVerificationUrl(User $user): string
    {
        return config('app.url') . '/api/confirm-email?token=' . $user->getRememberToken();
    }
}
