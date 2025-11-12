<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductTransferDetail
 * 
 * @property int $id
 * @property int $product_id
 * @property int $from_store
 * @property int $to_store
 * @property string $transfer_details
 * @property int $quantity_transfered
 * @property int $quantity_remained
 * @property string $status
 * @property int $transfered_by
 * @property Carbon $date_transfared
 * 
 * @property User $user
 * @property Product $product
 * @property Store $store
 *
 * @package App\Models
 */
class ProductTransferDetail extends Model
{
	protected $table = 'product_transfer_details';
	public $timestamps = false;

	protected $casts = [
		'product_id' => 'int',
		'from_store' => 'int',
		'to_store' => 'int',
		'quantity_transfered' => 'int',
		'quantity_remained'=> 'int',
		'transfered_by' => 'int'
	];

	protected $dates = [
		'date_transfared'
	];

	protected $fillable = [
		'product_id',
		'from_store',
		'to_store',
		'transfer_details',
		'quantity_transfered',
		'quantity_remained',
		'status',
		'transfered_by',
		'date_transfared'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'transfered_by');
	}

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function store()
	{
		return $this->belongsTo(Store::class, 'to_store');
	}
	public function store_from()
	{
		return $this->belongsTo(Store::class, 'from_store');
	}
}
