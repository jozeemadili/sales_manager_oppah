<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\BankDepositFile;
use Illuminate\Support\Facades\Storage;

/**
 * Streams an uploaded bank-deposit slip to logged-in users.
 */
class BankDepositSlipController extends Controller
{
    public function show($id)
    {
        $file = BankDepositFile::findOrFail($id);

        abort_unless(Storage::disk('public')->exists($file->file_path), 404);

        return Storage::disk('public')->response($file->file_path);
    }
}
