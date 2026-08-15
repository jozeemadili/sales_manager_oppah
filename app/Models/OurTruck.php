<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class OurTruck extends Model
{
    protected $table = 'our_trucks';
    public $timestamps = false;

    protected $casts = [
        'driver_id' => 'int',
        'created_by' => 'int'
    ];

    protected $fillable = [
        'plate_no',
        'driver_id',
        'created_by'
    ];

    /**
     * Relation to the user who created the truck record
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation to the user who is the driver
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
