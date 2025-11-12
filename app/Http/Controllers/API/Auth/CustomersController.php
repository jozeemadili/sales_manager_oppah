<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\Notifications\SMSController;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use App\TIRAClient\Scripts\Classes\Utils;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

class CustomersController extends Controller
{
    public function get()
    {
        $company_id      = Auth::user()->company_id;

        if($company_id == 1)
        {
            $customers = Customer::orderBy('id','desc')->paginate(10);
        }
        else 
        {
            $customers = Customer::whereIn('created_by', function ($query) use ($company_id)
            {
                $query->select('id')->from('users')->where('company_id', $company_id);
            })->orderBy('id','desc')->paginate(10);
        }

        if(substr(Route::getCurrentRoute()->uri,3) == "customers/registration")
        {
            return view('admin.customers.registration',['customers' => $customers]);
        }
        return $customers;
    }

    public function search(Request $request)
    {
        $customers = Customer::where('mobile', $request->phone_number)->orWhere('id_number', $request->id_number)->orWhere('first_name', $request->cname)->orWhere('middle_name', $request->cname)->orWhere('last_name', $request->cname)->orderBy('id','desc')->paginate(3);

        if(substr(Route::getCurrentRoute()->uri,3) == "customers/registration")
        {
            return view('admin.customers.registration',['customers' => $customers]);
        }
        return response()->json(['responseCode'=> 'SUCCESS','message'=>'Search Results Found', 'customers' => $customers]);
    }

    public function profile($id)
    {
        try
        {
            $customer = Customer::with('quotations.policies.claim_notifications')->find($id);
            return view('admin.customers.customer-profile',['customer' => $customer]);
        }
        catch(Exception $e){
            return array("responseCode" => "FAILED", "message" => $e->getMessage());
        }
    }

    public function register(Request $request)
    {
        $request->validate(
            [
                'id_number'     => ['required','unique:customers'],
                'type'          => ['in:1,2'],
                'gender'        => ['required','in:Male,Female'],
                'ward_id'       => ['exists:wards,id'],
                'role'          => ['in:Customer Admin,Customer Staff,Customer Audit,Others (N/A)'],
                'first_name'    => ['required', 'min:3'],
                'last_name'     => ['required', 'min:3'],
                'email'         => ['required', 'email','unique:customers'],
                // 'password'      => ['required','min:8','regex:/[a-z]/', 'regex:/[A-Z]/','regex:/[@$!%*#?&]/'],
                'password'      => ['required','min:6'],
                'mobile'        => ['required', 'min:9', 'max:10', 'unique:customers'],
                'dob'           => ['required', 'date'],
                'country_code'  => ['min:3', 'max:3']
            ]);

        $otp = substr((mt_rand(10000, 99999)), 0, 4);
        
        $Customer = Customer::create(
            [
                'gender'            => $request->gender,
                'country_code'      => $request->country_code != null ? $request->country_code : "TZA",
                'ward_id'           => $request->ward_id != null ? $request->ward_id : 282,
                'email'             => $request->email,
                'password'          => Hash::make($request->password),
                'first_name'        => $request->first_name,
                'middle_name'       => $request->middle_name,
                'last_name'         => $request->last_name,
                'type'              => $request->type != null ? $request->type : 1,
                'mobile'            => substr($request->mobile,0,1) == "0" ? substr($request->mobile, 1) : $request->mobile,
                'id_type'           => $request->id_type,
                'id_number'         => $request->id_number,
                'otp'               => md5($otp.Config::get('custom.constants.security.otp_salt')),
                'otp_created_at'    => date('Y-m-d H:i:s'),
                'dob'               => $request->dob,
                'is_mobile_verified'=> false,
                'role'              => ($request->role != null) ? $request->role :   "Customer Admin",
                'created_by'        => (Auth::user() != null) ? (intval(Auth::user()->id)) : null,
            ]);

            if(substr(Route::getCurrentRoute()->uri, 3) == "customers/add")
            {
                // return redirect()->route('customers-registration')->with('success', 'Customer <b>'.strtoupper($Customer->first_name.' '.$Customer->last_name).'</b> Successfully registered');
                return redirect()->route('customer-profile', ['id' => $Customer->id]);
            }

            $this->sendOTP($otp, $Customer);
            $token = $Customer->createToken('authToken')->plainTextToken;
            return response()->json(['responseCode'=> 'SUCCESS','message' => 'Customer Successfully Registered', 'customer' => $Customer, 'token'=> $token ]);
    }

    public function verifyOTP(Request $request)
    {
        //$utils = new Utils();
        $customer = Customer::find(Auth::user()->id);

        if($customer != null && !$customer->is_mobile_verified)
        {
            $entered_otp    = md5($request->otp.Config::get('custom.constants.security.otp_salt'));
            $actual_otp     = Auth::user()->otp;

            $otp_created_at = Carbon::parse($customer->otp_created_at);
            $otp_created_at->addMinutes(Config::get('custom.constants.security.otp_expiry_minutes'));

            if($entered_otp == $actual_otp && date('Y-m-d H:i:s') <= $otp_created_at)
            {
                $customer->mobile_verified_at     = date('Y-m-d H:i:s');
                $customer->is_mobile_verified = true;
                $customer->save();
    
                return array("responseCode" => "SUCESS", "message" => "Phone Number Verified Successfully !");
            }
            else 
            {
                //$utils->saveLogs("OTP", "OTP : ".$request->otp." | Diggested : ". md5($request->otp.Config::get('custom.constants.security.otp_salt'))." | In DB : ".$customer->otp);
                return array("responseCode" => "INVALID", "message" => "Invalid OTP or Expired !");
            }
        }
        else 
        {
            return array("responseCode" => "NOT_FOUND", "message" => "Customer Not Found Or OTP Already Verified !");
        }

    }

    public function resendOTP()
    {
        $customer = Customer::find(Auth::user()->id);

        if($customer != null)
        {
            if(!$customer->is_mobile_verified)
            {
                $otp                          = substr((mt_rand(10000, 99999)), 0, 4);
                $newOTP                       = md5($otp.Config::get('custom.constants.security.otp_salt'));
                $customer->otp                = $newOTP;
                $customer->otp_created_at     = date('Y-m-d H:i:s');
                $customer->save();
                $this->sendOTP($otp);
                return array("responseCode" => "SUCCESS", "message" => "OTP Resent Successfully !");
            }
            else 
            {
                return array("responseCode" => "VERIFIED", "message" => "Already Verified Previously !");
            } 
        }
        else 
        {
            return array("responseCode" => "NOT_FOUND", "message" => "Customer Not Found !");
        }
    }

    public function sendOTP($otp, $authenticatedUser = null)
    {
        $utils = new Utils();

        $user = $authenticatedUser == null ? Auth::user() : $authenticatedUser;

        if(!$user->is_mobile_verified)
        {
            $utils->saveLogs("OTP", "OTP : ".$otp." | ". md5($otp.Config::get('custom.constants.security.otp_salt'))." | ".$user->otp);
            
            $message       = "Dear ".ucfirst($user->first_name).", Use OTP ".$otp." to Verify your phone number";
            $SMSController = new SMSController();
            // return $SMSController->sendSandBox("255".$user->mobile, $message);
            try{
                return $SMSController->send("255".$user->mobile, $message);
            }
            catch(Exception $e){
                return array("responseCode" => "FAILED", "message" => $e->getMessage());
            } 
        }
        else 
        {
            return array("responseCode" => "VERIFIED", "message" => "Already Verified Previously !");
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate(['mobile' => ['required', 'min:9', 'max:10'], 'lastname' => ['required', 'min:3', 'max:15']]);
        $mobile = substr($request->mobile,0,1) == "0" ? substr($request->mobile,1) : $request->mobile;

        $user = Customer::where('mobile', $mobile)->where('last_name', 'like', '%'.strtolower($request->lastname).'%')->first();

        if($user != null)
        {
            $number         = substr((mt_rand(10000, 99999)), 0, 4);
            $password       = ucfirst(strtolower(substr($user->last_name,0,3))).$number;
            $hashed         = hash::make($password);
            $user->password = $hashed;
            $user->save();

            $message       = "Dear ".ucfirst($user->first_name).", Your new password is  ".$password."\nYou can change it later.";
            $SMSController = new SMSController();
            try
            {
                $SMSController->send("255".$user->mobile, $message);
                return array("responseCode" => "SUCCESS", "message" => "New Password sent to +255".$user->mobile);
            }
            catch(Exception $e)
            {
                return array("responseCode" => "FAILED", "message" => $e->getMessage());
            } 
        }
        else 
        {
            return array("responseCode" => "FAILED", "message" => "No Customer with 0".$mobile.' - '.$request->lastname);
        }
    }

    public function loginType($username)
    {
        $fieldType = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
        request()->merge([$fieldType => $username]);
        return $fieldType;
    }

    public function login(Request $request)
    {
        if($request['email'] == null || $request['email'] == "" || $request['password'] == null || $request['password'] == "")
        {
            return array("responseCode" => "FAILED", "message" => "All fields are required. Provided");
        }

        $loginType = $this->loginType($request['email']);
        $Customer = $loginType == "mobile" ? Customer::whereMobile($request['email'])->first() :  Customer::whereEmail($request['email'])->first();

        if (!$Customer)
        {
            return response()->json(['responseCode' =>'INVALID_CREDS', 'message' => 'Login information is invalid.'], 200);
        }

        $request['email'] = $Customer->email;

         $credentials = $request->only('email', 'password');

        if (!Auth::guard('webCustomer')->attempt($credentials))
        {
            return response()->json(['responseCode' =>'INVALID_CREDS', 'message' => 'Login information is invalid.'], 200);
        }

        $insurers    = Company::whereStatus('Active')->whereCategory('Insurer')->select('id', 'name', 'short_form', 'logo')->get();

        $token = $Customer->createToken('authToken')->plainTextToken;
        return response()->json(
            [
                'responseCode' => 'SUCCESS',
                'message'      => 'Logged In Successfully', 
                'user'         => $Customer, 
                'insurers'     => $insurers,
                'token'        => $token
            ]);
    }

    public function logout(Request $request)
    {
        try
        {
            $request->Customer()->currentAccessToken()->delete();
            return response()->json([
                'responseCode'  => 'SUCCESS',
                'message'       => 'Logged Out Successfully',
              ]);
        }
        catch(Exception $e)
        {
            return response()->json([
                'message'       => $e->getMessage(),
                'responseCode'  => 'LOGGED_OUT_EXCEPTION'
              ]);
        }
    }

    public function getCustomer(Request $request)
    {
        try
        {
            $Customer = $request->user();
            return response()->json([
                'responseCode'       => 'SUCCESS',
                'message'            => 'Authenticated User Fetched Successfully',
                'accessTokenInfo'    => array('created_at'=>$Customer->currentAccessToken()->created_at,'last_used_at'=>$Customer->currentAccessToken()->last_used_at),
                'customer'           => $Customer
              ]);
        }
        catch(Exception $e)
        {
            return response()->json([
                'responseCode'      => 'FAILED_TO_GET_CUSTOMER_INFORMATION_EXCEPTION',
                'message'           => $e->getMessage()
              ]);
        }
    }

    public static function resolveIdType($id_type)
    {
        return ($id_type == 1 ? "NIDA" : (($id_type == 2 ? "Voters" : ($id_type == 3 ? "PASSPORT" : ($id_type == 4 ? "DRIVING LICENCE" : ($id_type == 5 ? "ZANID" : ($id_type == 6 ? "TIN" : ($id_type == 7 ? "INCORPORATION CERTIFICATE" : "N/A"))))) )));
    }
  
    static function getDistrictName($code) {
        $districts = [
            "DIS1" => "ILALA",
            "DIS2" => "ILALA CBD",
            "DIS3" => "KIGAMBONI",
            "DIS4" => "KINONDONI",
            "DIS5" => "TEMEKE",
            "DIS6" => "UBUNGO",
            "DIS7" => "HANDENI",
            "DIS8" => "KILINDI",
            "DIS9" => "KOROGWE",
            "DIS10" => "LUSHOTO",
            "DIS11" => "MKINGA",
            "DIS12" => "MUHEZA",
            "DIS13" => "PANGANI",
            "DIS14" => "TANGA",
            "DIS15" => "TANGA CBD",
            "DIS16" => "ARUSHA",
            "DIS17" => "ARUSHA CBD",
            "DIS18" => "KARATU",
            "DIS19" => "LONGIDO",
            "DIS20" => "MERU",
            "DIS21" => "MONDULI",
            "DIS22" => "NGORONGORO",
            "DIS23" => "HAI",
            "DIS24" => "MOSHI",
            "DIS25" => "MOSHI CBD",
            "DIS26" => "MWANGA",
            "DIS27" => "ROMBO",
            "DIS28" => "SAME",
            "DIS29" => "SIHA",
            "DIS30" => "BABATI",
            "DIS31" => "BABATI CBD",
            "DIS32" => "HANANG'",
            "DIS33" => "KITETO",
            "DIS34" => "MBULU",
            "DIS35" => "SIMANJIRO",
            "DIS36" => "BUKOMBE",
            "DIS37" => "CHATO",
            "DIS38" => "GEITA",
            "DIS39" => "MBOGWE",
            "DIS40" => "NYANG'HWALE",
            "DIS41" => "BUNDA",
            "DIS42" => "BUTIAMA",
            "DIS43" => "MUSOMA CBD",
            "DIS44" => "RORYA",
            "DIS45" => "SERENGETI",
            "DIS46" => "TARIME",
            "DIS47" => "ILEMELA",
            "DIS48" => "KWIMBA",
            "DIS49" => "MAGU",
            "DIS50" => "MISUNGWI",
            "DIS51" => "NYAMAGANA",
            "DIS52" => "SENGEREMA",
            "DIS53" => "UKEREWE",
            "DIS54" => "BIHARAMULO",
            "DIS55" => "BUKOBA",
            "DIS56" => "BUKOBA CBD",
            "DIS57" => "KARAGWE",
            "DIS58" => "KYERWA",
            "DIS59" => "MISENYI",
            "DIS60" => "MULEBA",
            "DIS61" => "NGARA",
            "DIS62" => "KAHAMA",
            "DIS63" => "KISHAPU",
            "DIS64" => "SHINYANGA",
            "DIS65" => "SHINYANGA CBD",
            "DIS66" => "BARIADI",
            "DIS67" => "BUSEGA",
            "DIS68" => "ITILIMA",
            "DIS69" => "MASWA",
            "DIS70" => "MEATU",
            "DIS71" => "BAHI",
            "DIS72" => "CHAMWINO",
            "DIS73" => "CHEMBA",
            "DIS74" => "DODOMA",
            "DIS75" => "DODOMA CBD",
            "DIS76" => "KONDOA",
            "DIS77" => "KONGWA",
            "DIS78" => "MPWAPWA",
            "DIS79" => "IKUNGI",
            "DIS80" => "IRAMBA",
            "DIS81" => "MANYONI",
            "DIS82" => "MKALAMA",
            "DIS83" => "SINGIDA",
            "DIS84" => "SINGIDA CBD",
            "DIS85" => "IGUNGA",
            "DIS86" => "KALIUA",
            "DIS87" => "NZEGA",
            "DIS88" => "SIKONGE",
            "DIS89" => "TABORA CBD",
            "DIS90" => "URAMBO",
            "DIS91" => "UYUI",
            "DIS92" => "BUHIGWE",
            "DIS93" => "KAKONKO",
            "DIS94" => "KASULU",
            "DIS95" => "KIBONDO",
            "DIS96" => "KIGOMA",
            "DIS97" => "KIGOMA CBD",
            "DIS98" => "UVINZA",
            "DIS99" => "MLELE",
            "DIS100" => "MPANDA CBD",
            "DIS101" => "TANGANYIKA",
            "DIS102" => "IRINGA",
            "DIS103" => "IRINGA CBD",
            "DIS104" => "KILOLO",
            "DIS105" => "MUFINDI",
            "DIS106" => "CHUNYA",
            "DIS107" => "KYELA",
            "DIS108" => "MBARALI",
            "DIS109" => "MBEYA",
            "DIS110" => "MBEYA CBD",
            "DIS111" => "RUNGWE",
            "DIS112" => "ILEJE",
            "DIS113" => "MBOZI",
            "DIS114" => "MOMBA",
            "DIS115" => "SONGWE",
            "DIS116" => "KALAMBO",
            "DIS117" => "NKASI",
            "DIS118" => "SUMBAWANGA",
            "DIS119" => "SUMBAWANGA CBD",
            "DIS120" => "MBINGA",
            "DIS121" => "NYASA",
            "DIS122" => "SONGEA",
            "DIS123" => "SONGEA CBD",
            "DIS124" => "TUNDURU",
            "DIS125" => "LUDEWA",
            "DIS126" => "NJOMBE",
            "DIS127" => "NJOMBE CBD",
            "DIS128" => "WANGING'OMBE",
            "DIS129" => "BAGAMOYO",
            "DIS130" => "KIBAHA",
            "DIS131" => "KIBAHA CBD",
            "DIS132" => "KIBITI",
            "DIS133" => "KISARAWE",
            "DIS134" => "MAFIA",
            "DIS135" => "MKURANGA",
            "DIS136" => "RUFIJI",
            "DIS137" => "MASASI",
            "DIS138" => "MTWARA",
            "DIS139" => "MTWARA CBD",
            "DIS140" => "NANYUMBU",
            "DIS141" => "NEWALA",
            "DIS142" => "TANDAHIMBA",
            "DIS143" => "KILWA",
            "DIS144" => "LINDI",
            "DIS145" => "LINDI CBD",
            "DIS146" => "LIWALE",
            "DIS147" => "NACHINGWEA",
            "DIS148" => "RUANGWA",
            "DIS149" => "GAIRO",
            "DIS150" => "KILOMBERO",
            "DIS151" => "KILOSA",
            "DIS152" => "MALINYI",
            "DIS153" => "MOROGORO",
            "DIS154" => "MOROGORO CBD",
            "DIS155" => "MVOMERO",
            "DIS156" => "ULANGA",
            "DIS157" => "MAGHARIBI \"A\"",
            "DIS158" => "MAGHARIBI \"B\"",
            "DIS159" => "MJINI",
            "DIS160" => "KATI",
            "DIS161" => "KUSINI",
            "DIS162" => "KASKAZINI A",
            "DIS163" => "KASKAZINI B",
            "DIS164" => "CHAKECHAKE",
            "DIS165" => "MKOANI",
            "DIS166" => "MICHEWENI",
            "DIS167" => "WETE",
            "DIS168" => "MAKETE",
            "DIS170" => "NAMTUMBO",
            "DIS171" => "CHALINZE"
        ];
    
        return isset($districts[$code]) ? $districts[$code] : "Unknown District Code";
    }

   public static function getBranchName($branchCode) 
    {
        $branches = array(
            "001" => "MASDO",
            "002" => "KARIAKOO",
            "003" => "MWALONI",
            "004" => "ROCK CITY",
            "005" => "TEGETA",
            "006" => "KAHAMA",
            "008" => "MWENGE",
            "009" => "MOSHI",
            "010" => "ARUSHA CITY",
            "011" => "TCRA",
            "012" => "GEITA",
            "013" => "KATORO AGENCY",
            "014" => "LAMADI AGENCY",
            "015" => "KAGONGWA AGENCY",
            "016" => "TUNDUMA",
            "017" => "RWEGASORE",
            "018" => "SOKOINE BRANCH",
            "019" => "MOROGORO BRANCH",
            "029" => "INDUSTRIAL FINANCE BRANCH",
            "020" => "TPA SERVICE CENTRE",
            "022" => "ARUSHA BUSINESS CENTER",
            "023" => "MBEYA",
            "025" => "TANGA",
            "027" => "NARUGUSU",
            "028" => "SERENGETI SERVICE CENTRE",
            "030" => "DODOMA SERVICE CENTRE",
            "031" => "NAMANGA SERVICE CENTRE",
            "032" => "SABASABA SERVICE CENTRE",
            "035" => "LUMUMBA BRANCH",
            "038" => "KARUME"
        );

        if (array_key_exists($branchCode, $branches)) {
            return $branches[$branchCode];
        } else {
            return "Branch code not found";
        }
    }

    public function update(Request $request)
    {
        $Customer = Customer::find($request->customer_id);
        if(!$Customer)
        {
            return response()->json(['responseCode'=> 'NOT_FOUND', 'message' => "No Customer with ID : ".$request->customer_id]);
        }

        $request->validate(
            [
                'id_number'     => ['unique:customers'],
                'type'          => ['in:1,2'],
                'gender'        => ['in:Male,Female'],
                'ward_id'       => ['exists:wards,id'],
                'role'          => ['in:Customer Admin,Customer Staff,Customer Audit,Others (N/A)'],
                'first_name'    => ['min:3'],
                'last_name'     => ['min:3'],
                'email'         => ['email','unique:customers'],
                'password'      => ['min:8','regex:/[a-z]/', 'regex:/[A-Z]/','regex:/[@$!%*#?&]/'],
                'mobile'        => ['min:9', 'max:9', 'unique:customers'],
                'status'        => ['in:Active,Inactive,Pending,Rejected']
            ]);

         $Customer->id_number   = ($request->id_number != null) ? $request->id_number :  $Customer->id_number;
         $Customer->type        = ($request->type != null) ? $request->type :  $Customer->type;
         $Customer->gender      = ($request->gender != null) ? $request->gender :   $Customer->gender;
         $Customer->ward_id     = ($request->ward_id != null) ? $request->ward_id :   $Customer->ward_id;
         $Customer->role        = ($request->role != null) ? $request->role :   $Customer->role;
         $Customer->first_name  = ($request->first_name != null) ? $request->first_name :   $Customer->first_name;
         $Customer->last_name   = ($request->last_name != null) ? $request->last_name :   $Customer->last_name;
         $Customer->email       = ($request->email != null) ? $request->email :   $Customer->email;
         $Customer->password    = ($request->password != null) ? $request->password :   $Customer->password;
         $Customer->mobile      = ($request->mobile != null) ? $request->mobile :   $Customer->mobile;
         $Customer->status      = ($request->status != null) ? $request->status :  $Customer->status;
         $Customer->created_by  = (Auth::user() != null) ? (intval(Auth::user()->id)) : null;

         $Customer->save();

        return response()->json(['responseCode'=> 'SUCCESS','message'=>'Customer Details Updated Successfully', 'customer' => $Customer]);
    }
}
