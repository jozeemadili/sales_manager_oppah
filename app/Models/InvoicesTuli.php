<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InvoicesTuli
 * 
 * @property int $id
 * @property int $customer_id
 * @property Carbon $invoice_date
 * @property int $reg_by
 * @property string $status
 * @property int $company_id
 * @property string|null $paid_by
 * @property Carbon|null $date_paid
 * @property float|null $total_invoice_amount
 * @property float|null $amount_paid
 * @property float|null $amount_remained
 * 
 * @property Company $company
 * @property User $user
 * @property CustomersTuli $customers_tuli
 *
 * @package App\Models
 */
class InvoicesTuli extends Model
{
	protected $table = 'invoices_tuli';
	public $timestamps = false;

	protected $casts = [
		'customer_id' => 'int',
		'reg_by' => 'int',
		'company_id' => 'int',
		'total_invoice_amount' => 'float',
		'amount_paid' => 'float',
		'amount_remained' => 'float'
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
		'amount_remained'
	];

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'reg_by');
	}

	public function customers_tuli()
	{
		return $this->belongsTo(CustomersTuli::class, 'customer_id');
	}

	public function invoice_items_tuli()
{
    return $this->hasMany(InvoiceItemsTuli::class, 'invoice_id');
}

public function invoice_payment_details_tuli()
{
    return $this->hasMany(InvoicePaymentDetailsTuli::class, 'invoice_no');
}

}
