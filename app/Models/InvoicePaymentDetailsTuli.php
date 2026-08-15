<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InvoicePaymentDetailsTuli
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
 * @property InvoicesTuli $invoices_tuli
 *
 * @package App\Models
 */
class InvoicePaymentDetailsTuli extends Model
{
	protected $table = 'invoice_payment_details_tuli';
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

	public function invoices_tuli()
	{
		return $this->belongsTo(InvoicesTuli::class, 'invoice_no');
	}
}
