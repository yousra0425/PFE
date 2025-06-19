<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'description', 'teaching_level_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function teachingLevel()
    {
        return $this->belongsTo(TeachingLevel::class);
    }

    public function tutors()
    {
        return $this->belongsToMany(Tutor::class, 'tutor_subcategory');
    }
}

