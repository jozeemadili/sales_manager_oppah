<?php

namespace App\Http\Livewire\Components\Reports\General;

use App\Models\ClaimPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Claims extends Component
{
    public $claims;
    public $accessAll;

    public function render()
    {
        $company_id      = Auth::user()->company_id;
        $company_type    = Auth::user()->company->category;
        $role            = Auth::user()->role;
        $user_id         = Auth::user()->id;

        if($company_id == 1)
        {
            $today            = ClaimPayment::select('status', DB::raw('count(*) as qnty'), DB::raw('sum(paid_amount) as total'),)->where(DB::raw('DATE(created_at)'), DB::raw('DATE(now())'))->where(DB::raw('MONTH(created_at)'), DB::raw('MONTH(now())'))->where(DB::raw('YEAR(created_at)'), DB::raw('YEAR(now())'))->groupBy('status')->get();
            $thisMonth        = ClaimPayment::select('status', DB::raw('count(*) as qnty'), DB::raw('sum(paid_amount) as total'),)->where(DB::raw('MONTH(created_at)'), DB::raw('MONTH(now())'))->where(DB::raw('YEAR(created_at)'), DB::raw('YEAR(now())'))->groupBy('status')->get();
            $thisYear         = ClaimPayment::select('status', DB::raw('count(*) as qnty'), DB::raw('sum(paid_amount) as total'),)->where(DB::raw('YEAR(created_at)'), DB::raw('YEAR(now())'))->groupBy('status')->get();    
        }
        else 
        {
            $today            = ClaimPayment::select('status', DB::raw('count(*) as qnty'), DB::raw('sum(paid_amount) as total'),)->where(DB::raw('DATE(created_at)'), DB::raw('DATE(now())'))->where(DB::raw('MONTH(created_at)'), DB::raw('MONTH(now())'))->where(DB::raw('YEAR(created_at)'), DB::raw('YEAR(now())'))->whereCreatedBy($user_id)->groupBy('status')->get();
            $thisMonth        = ClaimPayment::select('status', DB::raw('count(*) as qnty'), DB::raw('sum(paid_amount) as total'),)->where(DB::raw('MONTH(created_at)'), DB::raw('MONTH(now())'))->where(DB::raw('YEAR(created_at)'), DB::raw('YEAR(now())'))->whereCreatedBy($user_id)->groupBy('status')->get();
            $thisYear         = ClaimPayment::select('status', DB::raw('count(*) as qnty'), DB::raw('sum(paid_amount) as total'),)->where(DB::raw('YEAR(created_at)'), DB::raw('YEAR(now())'))->whereCreatedBy($user_id)->groupBy('status')->get();    
        }

        $this->claims = array('today' => $today, 'thisMonth' => $thisMonth, 'thisYear' => $thisYear);
        
        return view('livewire.components.reports.general.claims');
    }
}