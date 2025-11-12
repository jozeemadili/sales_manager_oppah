<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\InvoicePaymentDetail;

/**
 * Class Invoice
 * 
 * @property int $id
 * @property int $customer_id
 * @property Carbon $invoice_date
 * @property Carbon $date_paid
 * @property int $reg_by
 * @property string $status
 * @property int $company_id
 * @property string | null $status
 * @property string | null $paid_by
 * 
 * @property float $total_invoice_amount
 * @property float $amount_paid
 * @property float $amount_remained
 * 
 * @property Customer $customer
 * @property User $user
 * @property Company $company
 * @property Collection|InvoiceItem[] $invoice_items
 *
 * @package App\Models
 */
class Invoice extends Model
{
	protected $table = 'invoices';
	public $timestamps = false;

	protected $casts = [
		'customer_id' => 'int',
		'reg_by' => 'int',
		'company_id' => 'int'
	];

	protected $dates = [
		'invoice_date',
		'date_paid'
	];

	protected $fillable = [
		'customer_id',
		'invoice_date',
		'reg_by',
		'status',
		'company_id',
		'paid_by',
		'date_paid',
		'total_invoice_amount',
		'amount_paid',
		'amount_remained',
	];

	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'reg_by');
	}

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function invoice_items()
	{
		return $this->hasMany(InvoiceItem::class);
	}

	public function invoice_payment_details()
    {
        return $this->hasMany(InvoicePaymentDetail::class, 'invoice_no');
    }
}
