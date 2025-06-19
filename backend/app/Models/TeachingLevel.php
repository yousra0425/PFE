<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeachingLevel extends Model
{
    protected $fillable = ['name'];

    public function tutors()
    {
        return $this->hasMany(Tutor::class);
    }
}
