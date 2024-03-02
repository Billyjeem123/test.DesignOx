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

    public mixed $user_id;
    protected $table = 'tblusers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fullname',
        'email',
        'phone_number',
        'password',
        'confirm_password',
        'otp',
        'account_type',
        'google_id',
        'country'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
        'email_verified_at',
        'created_at',
        'updated_at'
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
     * Check if the user has a specific role.
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('role_name', $roleName)->exists();
    }



    /**
     * The roles that belong to the user.
     * #User has many roles
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
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
