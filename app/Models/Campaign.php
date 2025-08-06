<?php
// app/Models/Campaign.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model {
    protected $fillable = ['name','description','start_date','end_date','active'];
    protected $casts = [
        'active' => 'boolean',
          'start' => 'date:Y-m-d',
    'end' => 'date:Y-m-d',
    ];
}
