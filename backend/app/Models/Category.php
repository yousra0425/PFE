<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model 
{
    use HasFactory;

    protected $fillable = [
        'label', 
        'description',

    ];

    /*public function services(){
    return $this->hasMany(Service::class);
    }*/

    public function tutors(){
        return $this->belongsToMany(Tutor::class, 'tutor_category');
    }

    public function subcategories(){
        return $this->hasMany(Subcategory::class);
    }



}