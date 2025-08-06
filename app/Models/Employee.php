<?php
// app/Models/Employee.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'name', 'role', 'phone', 'email', 'avatar',
    ];

    // Optional: attendance relationship if you want to link to attendance later
    // public function attendances() {
    //     return $this->hasMany(Attendance::class);
    // }
}
