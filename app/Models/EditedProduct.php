<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EditedProduct
 * 
 * @property int $id
 * @property int $product_id
 * @property string $product_name
 * @property int $qty
 * @property int $category
 * @property float $purchasing_price
 * @property float $selling_price
 * @property string $description
 * @property string|null $unit_of_measuer
 * @property int $edited_by
 * @property string $reason_for_editing
 * @property Carbon $date_edited
 * 
 * @property Product $product
 * @property User $user
 *
 * @package App\Models
 */
class EditedProduct extends Model
{
	protected $table = 'edited_products';
	public $timestamps = false;

	protected $casts = [
		'product_id' => 'int',
		'qty' => 'int',
		'category' => 'int',
		'purchasing_price' => 'float',
		'selling_price' => 'float',
		'edited_by' => 'int'
	];

	protected $dates = [
		'date_edited'
	];

	protected $fillable = [
		'product_id',
		'product_name',
		'qty',
		'category',
		'purchasing_price',
		'selling_price',
		'description',
		'unit_of_measuer',
		'edited_by',
		'reason_for_editing',
		'date_edited'
	];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'edited_by');
	}

	public function category()
	{
		return $this->belongsTo(Category::class, 'category');
	}
}
