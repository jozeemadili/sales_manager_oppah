<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InvoicePaymentDetail
 * 
 * @property int $id
 * @property int $invoice_no
 * @property float $amount_submitted
 * @property Carbon $date_payed
 * @property string $status
 * @property string|null $payer_id
 * @property string|null $receipt_number
 * @property string|null $channel
 * 
 * @property Invoice $invoice
 *
 * @package App\Models
 */
class InvoicePaymentDetail extends Model
{
	protected $table = 'invoice_payment_details';
	public $timestamps = false;

	protected $casts = [
		'invoice_no' => 'int',
		'amount_submitted' => 'float'
	];

	protected $dates = [
		'date_payed'
	];

	protected $fillable = [
		'invoice_no',
		'amount_submitted',
		'date_payed',
		'status',
		'payer_id',
		'receipt_number',
		'channel'
	];

	public function invoice()
	{
		return $this->belongsTo(Invoice::class, 'invoice_no');
	}
}
