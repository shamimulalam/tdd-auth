<?php

namespace App\Repositories;

use App\Enums\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRepository
{
    /**
     * @var $user User
     */
    private User $user;

    public function __construct()
    {
        $this->user = (new User());
    }

    public function createNewUser(array $data): mixed
    {
        return $this->user->create($data);
    }

    public function getUserByRememberTokenAndStatusPending(string $rememberToken)
    {
        return $this->user->where('status', '=', Status::Pending)
                          ->where('remember_token', '=', $rememberToken)
                          ->first();
    }

    public function getUserByEmail(string $email): User|ModelNotFoundException
    {
        return $this->user->where('email', '=', $email)->firstOrFail();
    }
}
