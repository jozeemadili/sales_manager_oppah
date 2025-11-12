<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
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
 * @property string|null $unit_of_measuer
 * @property float|null $qty_sold
 * @property float|null $invoiced_qty
 * @property float|null $qty_remained
 * @property int|null $inventory_id

 * 
 * @property Company $company
 * @property Store $store
 * @property User $user
 * @property Collection|InvoiceItem[] $invoice_items
 * @property Collection|Sale[] $sales
 *
 * @package App\Models
 */
class Product extends Model
{
	protected $table = 'products';
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
		'inventory_id'  => 'int'
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

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function category()
	{
		return $this->belongsTo(Category::class, 'category');
	}

	public function store()
	{
		return $this->belongsTo(Store::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function invoice_items()
	{
		return $this->hasMany(InvoiceItem::class);
	}
	public function edited_products()
	{
		return $this->hasMany(EditedProduct::class);
	}

	public function sales()
	{
		return $this->hasMany(Sale::class);
	}
}
