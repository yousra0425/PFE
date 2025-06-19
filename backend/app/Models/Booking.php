<?php

// app/Models/Booking.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tutor_id',
        'date',
        'start_time',
        'end_time',
        'duration',
        'message',
        'status',
        'client_lat',
        'client_lng',
    ];
    

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }

    // The tutor assigned to the booking
    public function tutor()
    {
        return $this->belongsTo(User::class, 'tutor_id'); 
    }
}
