<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Tutor extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'user_id',
        'email',
        'password', // keep if tutors can log in
        'bio',
        'hourly_rate',
        'telephone',
        'experience_years',
        'profile_picture',
        'rating',
        'cin_image',
        'is_verified',
        'available',
        'latitude',
        'longitude',
        'category_id',
        'subcategory_id',
        'teaching_level_id',
        'working_radius'
    ];

    protected $hidden = [
        'password',
    ];
    
    protected $appends = ['first_name', 'last_name'];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function teachingLevel()
    {
        return $this->belongsTo(TeachingLevel::class, 'teaching_level_id');
    }



    // Accessors for convenience
    public function getFirstNameAttribute()
    {
        return $this->user->first_name ?? null;
    }

    public function getLastNameAttribute()
    {
        return $this->user->last_name ?? null;
    }
}
