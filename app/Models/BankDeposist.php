<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BankDeposist
 * 
 * @property int $id
 * @property string $bank_name
 * @property float $deposited_amount
 * @property int $deposited_by
 * @property string $status
 * @property Carbon $deposited_date
 * @property string $file_path
 * @property string $deposit_origin
 * @property Carbon|null $created_at
 * 
 * @property User $user
 *
 * @package App\Models
 */
class BankDeposist extends Model
{
	protected $table = 'bank_deposists';
	public $timestamps = false;

	protected $casts = [
		'deposited_amount' => 'float',
		'deposited_by' => 'int'
	];

	protected $dates = [
		'deposited_date',
		'balance_date',
	];

	protected $fillable = [
		'bank_name',
		'deposited_amount',
		'deposited_by',
		'status',
		'deposited_date',
		'file_path',
		'deposit_origin',
		'created_at',
		'account_number',
		'store_id',
		'source',
		'balance_date',
	];

	// Deposits of the Mbao daily balance, recorded from the Mbao dashboard.
	const SOURCE_MBAO = 'MBAO';

	public function user()
	{
		return $this->belongsTo(User::class, 'deposited_by');
	}
	public function files()
    {
        return $this->hasMany(BankDepositFile::class, 'deposit_id');
    }
}
