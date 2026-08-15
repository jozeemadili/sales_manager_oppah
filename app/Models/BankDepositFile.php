<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BankDepositFile
 * 
 * @property int $id
 * @property int $deposit_id
 * @property string $file_path
 * @property Carbon $created_at
 * 
 * @property BankDeposist $bank_deposist
 *
 * @package App\Models
 */
class BankDepositFile extends Model
{
	protected $table = 'bank_deposit_files';
	public $timestamps = false;

	protected $casts = [
		'deposit_id' => 'int'
	];

	protected $fillable = [
		'deposit_id',
		'file_path'
	];

	public function bank_deposist()
	{
		return $this->belongsTo(BankDeposist::class, 'deposit_id');
	}

	 // Helper: return full URL preview
	 public function getFileUrlAttribute()
	 {
		 return Storage::url($this->file_path);
	 }
}
