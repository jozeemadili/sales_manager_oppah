<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InvoiceItem
 * 
 * @property int $id
 * @property int $invoice_id
 * @property int $product_id
 * @property float $qty
 * @property float $price
 * @property string $status
 * @property string|null $discount
 * @property string|null $paid_by
 * @property int|null $discounte_by
 * @property Carbon|null $date_discounted
 * @property Carbon|null $created_at
 * @property Carbon|null $date_paid
 * 
 * 
 * 
 * @property Invoice $invoice
 * @property Product $product
 * @property User|null $user
 *
 * @package App\Models
 */
class InvoiceItem extends Model
{
	protected $table = 'invoice_items';
	public $timestamps = false;

	protected $casts = [
		'invoice_id' => 'int',
		'product_id' => 'int',
		'qty' => 'float',
		'price' => 'float',
		'discounte_by' => 'int'
	];

	protected $dates = [
		'date_discounted',
		'created_at',
		'date_paid'
	];

	protected $fillable = [
		'invoice_id',
		'product_id',
		'qty',
		'price',
		'status',
		'discount',
		'discounte_by',
		'date_discounted',
		'created_at',
		'paid_by'
	];

	public function invoice()
	{
		return $this->belongsTo(Invoice::class);
	}

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'discounte_by');
	}
}
