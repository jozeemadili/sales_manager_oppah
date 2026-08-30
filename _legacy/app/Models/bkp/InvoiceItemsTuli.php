<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InvoiceItemsTuli
 * 
 * @property int $id
 * @property int $invoice_id
 * @property int $product_id
 * @property float $qty
 * @property float $price
 * @property string $status
 * @property string|null $discount
 * @property int|null $discounte_by
 * @property Carbon|null $date_discounted
 * @property Carbon|null $created_at
 * @property string|null $paid_by
 * @property Carbon|null $date_paid
 * 
 * @property InvoicesTuli $invoices_tuli
 *
 * @package App\Models
 */
class InvoiceItemsTuli extends Model
{
	protected $table = 'invoice_items_tuli';
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
		'paid_by',
		'date_paid'
	];

	public function invoices_tuli()
	{
		return $this->belongsTo(InvoicesTuli::class, 'invoice_id');
	}

	public function products_tuli()
	{
		return $this->belongsTo(ProductsTuli::class);
	}
	
}
