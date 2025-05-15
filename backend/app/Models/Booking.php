<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'service_name', 'provider_name', 'date', 'time'];

    public function user(){
    return $this->belongsTo(User::class);
   }
}
