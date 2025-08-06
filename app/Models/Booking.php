<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'room_id',
        'check_in',
        'check_out',
        'adults',
        'children',
        'payment_method',
        'total',
        'status',
        'paid_amount',
    ];

    // Format the date without the time part (if needed)
    public function formattedDate()
    {
        return Carbon::parse($this->date)->format('Y-m-d');
    }

    // Format the start time
    public function formattedStartTime()
    {
        return Carbon::parse($this->start_time)->format('H:i');
    }

    // Format the end time
    public function formattedEndTime()
    {
        return Carbon::parse($this->end_time)->format('H:i');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
