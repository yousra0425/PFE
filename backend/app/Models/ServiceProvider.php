<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'service_type',
        'experience',
        'description',
        'phone',
        'email',     
        'location',
        'latitude',
        'longitude',
        'rating',
        'cin_image',
        'is_verified',
    ];

    
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function reviews(){
    return $this->hasMany(Review::class);
    }

    public function services(){
    return $this->belongsToMany(Service::class, 'service_provider_service');
    }

}
