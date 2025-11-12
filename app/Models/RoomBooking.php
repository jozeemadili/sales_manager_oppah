<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RoomBooking
 * 
 * @property int $id
 * @property int $customer_id
 * @property int $room_id
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property float $price
 * @property string $status
 * @property int $sold_by
 * @property int $company_id
 * 
 * @property Company $company
 * @property HotelCustomer $hotel_customer
 * @property User $user
 * @property Room $room
 *
 * @package App\Models
 */
class RoomBooking extends Model
{
	protected $table = 'room_bookings';
	public $timestamps = false;

	protected $casts = [
		'customer_id' => 'int',
		'room_id' => 'int',
		'price' => 'float',
		'sold_by' => 'int',
		'company_id' => 'int'
	];

	protected $dates = [
		'start_date',
		'end_date'
	];

	protected $fillable = [
		'customer_id',
		'room_id',
		'start_date',
		'end_date',
		'price',
		'status',
		'sold_by',
		'company_id'
	];

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function hotel_customer()
	{
		return $this->belongsTo(HotelCustomer::class, 'customer_id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'sold_by');
	}

	public function room()
	{
		return $this->belongsTo(Room::class);
	}
}
