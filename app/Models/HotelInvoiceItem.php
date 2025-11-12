<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HotelInvoiceItem
 * 
 * @property int $id
 * @property int $room_id
 * @property int $invoice_id
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property float $price
 * @property string $status
 * @property string|null $discount
 * @property int|null $discounte_by
 * @property string|null $paid_by
 * @property Carbon|null $date_discounted
 * @property Carbon $created_at
 * @property Carbon $date_paid
 * 
 * 
 * @property Room $room
 * @property HotelInvoice $hotel_invoice
 * @property User|null $user
 *
 * @package App\Models
 */
class HotelInvoiceItem extends Model
{
	protected $table = 'hotel_invoice_items';
	public $timestamps = false;

	protected $casts = [
		'room_id' => 'int',
		'invoice_id' => 'int',
		'price' => 'float',
		'discounte_by' => 'int'
		
	];

	protected $dates = [
		'start_date',
		'end_date',
		'date_discounted',
		'date_paid'
	];

	protected $fillable = [
		'room_id',
		'invoice_id',
		'start_date',
		'end_date',
		'price',
		'status',
		'discount',
		'discounte_by',
		'date_discounted',
		'created_at',
		'date_paid',
		'paid_by'
	];

	public function room()
	{
		return $this->belongsTo(Room::class);
	}

	public function hotel_invoice()
	{
		return $this->belongsTo(HotelInvoice::class, 'invoice_id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'discounte_by');
	}
}
