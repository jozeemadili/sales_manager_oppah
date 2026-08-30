<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SalesTuli
 * 
 * @property int $id
 * @property int $product_id
 * @property float $quantity
 * @property float $selling_price
 * @property int $sold_by
 * @property string $status
 * @property Carbon $date_sold
 * @property int $company_id
 * @property int $customer_id
 * @property int|null $invoice_issued_id
 * 
 * @property ProductsTuli $products_tuli
 * @property CustomersTuli $customers_tuli
 * @property InvoicesTuli|null $invoices_tuli
 * @property Company $company
 *
 * @package App\Models
 */
class SalesTuli extends Model
{
	protected $table = 'sales_tuli';
	public $timestamps = false;

	protected $casts = [
		'product_id' => 'int',
		'quantity' => 'float',
		'selling_price' => 'float',
		'sold_by' => 'int',
		'company_id' => 'int',
		'customer_id' => 'int',
		'invoice_issued_id' => 'int',
		'original_price' => 'float',
		'discount_percent' => 'float',
		'discount_amount' => 'float',
	];

	protected $dates = [
		'date_sold'
	];

	protected $fillable = [
		'product_id',
		'quantity',
		'selling_price',
		'sold_by',
		'status',
		'date_sold',
		'company_id',
		'customer_id',
		'invoice_issued_id',
		'original_price',
		'discount_percent',
		'discount_amount',
	];

	public function products_tuli()
	{
		return $this->belongsTo(ProductsTuli::class, 'product_id');
	}

	public function customers_tuli()
	{
		return $this->belongsTo(CustomersTuli::class, 'customer_id');
	}

	public function invoices_tuli()
	{
		return $this->belongsTo(InvoicesTuli::class, 'invoice_issued_id');
	}

	public function company()
	{
		return $this->belongsTo(Company::class);
	}
}
