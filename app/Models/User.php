<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tblusers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'otp',
        'account_type',
        'google_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The roles that belong to the user.
     * #User has many roles
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'tbluser_role', 'user_id', 'role_id');
    }


     /**
     * The sanctum token that belong to the user.
     * #U
     */

    public function accessToken()
    {
        return $this->morphMany(PersonalAccessToken::class, 'tokenable');
    }
 /**
     * The fetches current token associated with the user
     */
    public function getCurrentToken()
    {
        // Retrieve the latest access token associated with the user
        $latestAccessToken = $this->accessToken()->latest()->first();

        return $latestAccessToken;
    }
}
