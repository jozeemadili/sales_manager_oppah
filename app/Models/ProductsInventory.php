<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductsInventory
 * 
 * @property int $id
 * @property string $product_name
 * @property int $qty
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
 * @property int|null $qty_sold
 * @property int|null $invoiced_qty
 * @property int|null $qty_remained
 * @property string|null $unit_of_measuer
 * @property int|null $inventory_id
 * 
 * @property Inventory|null $inventory
 * @property Company $company
 * @property User $user
 * @property Store $store
 *
 * @package App\Models
 */
class ProductsInventory extends Model
{
	protected $table = 'products_inventories';
	public $timestamps = false;

	protected $casts = [
		'qty' => 'int',
		'category' => 'int',
		'store_id' => 'int',
		'purchasing_price' => 'float',
		'selling_price' => 'float',
		'company_id' => 'int',
		'created_by' => 'int',
		'qty_sold' => 'int',
		'invoiced_qty' => 'int',
		'qty_remained' => 'int',
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

	public function inventory()
	{
		return $this->belongsTo(Inventory::class);
	}

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function category()
	{
		return $this->belongsTo(Category::class, 'category');
	}

	public function store()
	{
		return $this->belongsTo(Store::class);
	}
}
