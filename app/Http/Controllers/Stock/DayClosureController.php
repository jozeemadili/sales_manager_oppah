<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\DayClosure;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Mbao End Day report (PDF), built from the snapshot saved at closing so a
 * re-print always shows the figures as they were when the day was closed.
 */
class DayClosureController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            abort_unless($user->role === 'Mbao' || $user->hasFullAccess(), 403);

            return $next($request);
        });
    }

    public function pdf($id)
    {
        $closure = DayClosure::with(['closer', 'reopener'])->findOrFail($id);
        $snapshot = $closure->snapshot ?? [];

        // Absolute paths so slip photos can be embedded in the PDF.
        foreach ($snapshot['deposits'] ?? [] as $i => $deposit) {
            foreach ($deposit['slips'] ?? [] as $j => $slip) {
                $exists = $slip['image'] && Storage::disk('public')->exists($slip['path']);
                $snapshot['deposits'][$i]['slips'][$j]['file'] = $exists ? Storage::disk('public')->path($slip['path']) : null;
            }
        }

        $pdf = Pdf::setOption(['isFontSubsettingEnabled' => true])
            ->loadView('admin.sales_management.day-closure-pdf', [
                'closure' => $closure,
                's' => $snapshot,
                'company' => Company::find(Auth::user()->company_id),
            ]);

        return $pdf->stream('end_day_'.$closure->business_date->format('Ymd').'_'.$closure->id.'.pdf');
    }
}
