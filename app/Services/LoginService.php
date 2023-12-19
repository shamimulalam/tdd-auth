<?php

namespace App\Services;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;

class LoginService extends CoreService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    /**
     * @throws ModelNotFoundException|AuthenticationException
     */
    public function processLogin(LoginRequest $request): User
    {
        $user = $this->userRepository->getUserByEmail($request->get('email'));

        if (!Hash::check($request->get('password'), $user->getAuthPassword())) {
            throw new AuthenticationException('Wrong password');
        }

        return $user;
    }
}
