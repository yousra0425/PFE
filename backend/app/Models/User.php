<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasApiTokens, HasFactory, Notifiable;
    
    public const ROLE_HIERARCHY = [
        'client' => 1,
        'service_provider' => 2,
        'admin' => 3,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'telephone',
        'date_of_birth',
        'address',
        'email',
        'password',
        'city',
        'cin',
        'cin_status',
        'role', 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function Booking(){

    return $this->hasMany(Booking::class);
    }


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
   
}
