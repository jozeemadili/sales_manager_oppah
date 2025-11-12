<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Room
 * 
 * @property int $id
 * @property string $room_name
 * @property string $status
 * @property int $created_by
 * @property Carbon $created_at
 * @property int $category_id
 * @property int $company_id
 * @property string $occupied
 * 
 * @property User $user
 * @property RoomsCategory $rooms_category
 * @property Company $company
 * @property Collection|HotelInvoiceItem[] $hotel_invoice_items
 * @property Collection|RoomBooking[] $room_bookings
 *
 * @package App\Models
 */
class Room extends Model
{
	protected $table = 'rooms';
	public $timestamps = false;

	protected $casts = [
		'created_by' => 'int',
		'category_id' => 'int',
		'company_id' => 'int'
	];

	protected $fillable = [
		'room_name',
		'status',
		'created_by',
		'category_id',
		'company_id',
		'occupied',
		'created_at'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function rooms_category()
	{
		return $this->belongsTo(RoomsCategory::class, 'category_id');
	}

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function hotel_invoice_items()
	{
		return $this->hasMany(HotelInvoiceItem::class);
	}

	public function room_bookings()
	{
		return $this->hasMany(RoomBooking::class);
	}
}
