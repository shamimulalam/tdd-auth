<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @package User
 * @property integer $id
 * @property string  $first_name
 * @property string  $last_name
 * @property Carbon  $date_of_birth
 * @property string  $email
 * @property Carbon  $email_verified_at
 * @property string  $remember_token
 * @method string getFullName
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'email',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'first_name'        => 'string',
        'last_name'         => 'string',
        'date_of_birth'     => 'string',
        'email'             => 'string',
    ];

    public function scopeGetFullName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
