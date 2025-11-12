<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Branch
 * 
 * @property int $id
 * @property string $branch_id
 * @property string $branch_name
 * @property string $status
 * @property int $company_id
 *
 * @package App\Models
 */
class Branch extends Model
{
	protected $table = 'branches';
	public $timestamps = false;

	protected $casts = [
		'company_id' => 'int'
	];

	protected $fillable = [
		'branch_id',
		'branch_name',
		'status',
		'company_id'
	];
}
