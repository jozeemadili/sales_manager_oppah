<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductsTuli
 * 
 * @property int $id
 * @property string $product_name
 * @property float $qty
 * @property int $category
 * @property int $store_id
 * @property string $barcode
 * @property float $purchasing_price
 * @property float $selling_price
 * @property string|null $description
 * @property int $company_id
 * @property Carbon|null $expire_date
 * @property int $created_by
 * @property Carbon $reg_at
 * @property string $status
 * @property float|null $qty_sold
 * @property float|null $invoiced_qty
 * @property float|null $qty_remained
 * @property string|null $unit_of_measuer
 * @property int|null $inventory_id
 * 
 * @property CategoriesTuli $categories_tuli
 * @property Company $company
 * @property InventoriesTuli|null $inventories_tuli
 * @property StoresTuli $stores_tuli
 *
 * @package App\Models
 */
class ProductsTuli extends Model
{
	protected $table = 'products_tuli';
	public $timestamps = false;

	protected $casts = [
		'qty' => 'float',
		'category' => 'int',
		'store_id' => 'int',
		'purchasing_price' => 'float',
		'selling_price' => 'float',
		'company_id' => 'int',
		'created_by' => 'int',
		'qty_sold' => 'float',
		'invoiced_qty' => 'float',
		'qty_remained' => 'float',
		'inventory_id' => 'int'
	];

	protected $dates = [
		'expire_date',
		'reg_at'
	];

	protected $fillable = [
		'product_name',
		'qty',
		'category',
		'store_id',
		'barcode',
		'purchasing_price',
		'selling_price',
		'description',
		'company_id',
		'expire_date',
		'created_by',
		'reg_at',
		'status',
		'qty_sold',
		'invoiced_qty',
		'qty_remained',
		'unit_of_measuer',
		'inventory_id'
	];

	public function categories_tuli()
	{
		return $this->belongsTo(CategoriesTuli::class, 'category');
	}

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function inventories_tuli()
	{
		return $this->belongsTo(InventoriesTuli::class, 'inventory_id');
	}

	public function stores_tuli()
	{
		return $this->belongsTo(StoresTuli::class, 'store_id');
	}
	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}
}
