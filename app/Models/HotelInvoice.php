<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HotelInvoice
 * 
 * @property int $id
 * @property int $customer_id
 * @property Carbon $invoice_date
 * @property Carbon | null $date_paid
 * 
 * @property int $reg_by
 * @property string $status
 * @property int $company_id
 * 
 * @property Company $company
 * @property HotelCustomer $hotel_customer
 * @property User $user
 * @property Collection|HotelInvoiceItem[] $hotel_invoice_items
 *
 * @package App\Models
 */
class HotelInvoice extends Model
{
	protected $table = 'hotel_invoices';
	public $timestamps = false;

	protected $casts = [
		'customer_id' => 'int',
		'reg_by' => 'int',
		'company_id' => 'int'
	];

	protected $dates = [
		'invoice_date'
	];

	protected $fillable = [
		'customer_id',
		'invoice_date',
		'reg_by',
		'status',
		'company_id',
		'paid_by',
		'date_paid'
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
		return $this->belongsTo(User::class, 'reg_by');
	}

	public function hotel_invoice_items()
	{
		return $this->hasMany(HotelInvoiceItem::class, 'invoice_id');
	}
}
