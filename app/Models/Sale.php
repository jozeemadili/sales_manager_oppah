<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Sale
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
 * 
 * @property Company $company
 * @property Product $product
 * @property User $user
 * @property Customer $customer
 *
 * @package App\Models
 */
class Sale extends Model
{
	protected $table = 'sales';
	public $timestamps = false;

	protected $casts = [
		'product_id' => 'int',
		'quantity' => 'float',
		'selling_price' => 'float',
		'sold_by' => 'int',
		'company_id' => 'int',
		'customer_id' => 'int',
		'invoice_issued_id' => 'int'
		
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
		'invoice_issued_id'
	];

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'sold_by');
	}

	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}
}
