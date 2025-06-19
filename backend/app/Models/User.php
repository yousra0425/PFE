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
    use HasFactory, Notifiable, HasApiTokens;

    
    public const ROLE_HIERARCHY = [
        'client' => 1,
        'tutor' => 2,
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
        'latitude',
        'longitude',
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
    
    public function tutor(){
    return $this->hasOne(Tutor::class);
    } 
    
    public function reviews()
    {
        return $this->hasMany(Review::class, 'tutor_id');
    }
    


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
   
}
