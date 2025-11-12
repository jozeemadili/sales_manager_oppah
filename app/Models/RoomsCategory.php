<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RoomsCategory
 * 
 * @property int $id
 * @property string $category_name
 * @property string $status
 * @property string $currency
 * @property int $created_by
 * @property Carbon $created_at
 * @property int $company_id
 * @property int $hotel_id
 * @property string $ammenties
 * @property float $price_day
 * 
 * 
 * @property User $user
 * @property Company $company
 * @property MyHotel $my_hotel
 *
 * @package App\Models
 */
class RoomsCategory extends Model
{
	protected $table = 'rooms_categories';
	public $timestamps = false;

	protected $casts = [
		'created_by' => 'int',
		'company_id' => 'int',
		'hotel_id' => 'int',
		'price_day' => 'float'
	];

	protected $fillable = [
		'category_name',
		'status',
		'created_by',
		'company_id',
		'hotel_id',
		'ammenties',
		'created_at',
		'price_day',
		'currency'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function my_hotel()
	{
		return $this->belongsTo(MyHotel::class, 'hotel_id');
	}
}
