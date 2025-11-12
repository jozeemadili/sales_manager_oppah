<?php
use App\Http\Controllers\API\Auth\CustomersController;
use App\Http\Controllers\API\Auth\PortalUsersController;
use App\Http\Controllers\API\Companies\BranchesController;
use App\Http\Controllers\API\Places\PlacesController;
use App\Http\Controllers\API\Products\ProductsController;
use App\Http\Controllers\API\Products\RisksController as ProductsRisksController;
use App\Http\Controllers\API\Quotations\QuotationsController;
use App\Http\Controllers\Payments\PaymentsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Tests\OpenAIs\DalleControllers;

Route::get("/Test/Dalle/Test", [DalleControllers::class, 'test']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



//Portal Users Auth
Route::post('v1/Auth', [PortalUsersController::class, 'login']);

//Customers Auth
Route::post('v1/Customers/SelfRegistration',[CustomersController::class, 'register']);
Route::post('v1/Customers/Auth', [CustomersController::class, 'login']);
Route::post('v1/Customers/Auth/PasswordReset', [CustomersController::class, 'resetPassword']);

Route::get('v1/policies/quotations/receipt/{id}',[QuotationsController::class, 'streamPdfReceipt'])->name('quotation-stream');




Route::middleware(['auth:sanctum'])->group(function () 
{
        //Customer Quotations
        Route::post('v1/Quotations/CustomersQuotations',[QuotationsController::class, 'CustomersQuotations']);
        Route::post('v1/Quotations/CustomersQuotations/Register',[QuotationsController::class, 'RegisterCustomerQuotation']);

         //Verify OTP
         Route::post('v1/Customers/VerifyOTP',[CustomersController::class, 'VerifyOTP']);

         //Resend OTP
         Route::post('v1/Customers/ResendOTP',[CustomersController::class, 'ResendOTP']);
});


Route::group(['prefix' => 'v1/','middleware' => ['auth:sanctum']], function()
{
    //Portal Users Auth
    Route::post('Register',[PortalUsersController::class, 'register']);
    Route::post('Users',[PortalUsersController::class, 'get']);
    Route::post('Authenticated',[PortalUsersController::class, 'getUser']);
    Route::post('Logout',[PortalUsersController::class, 'logout']);
    Route::post('Update',[PortalUsersController::class, 'update']);

    //Customers Auth
    Route::post('Customers/Register',[CustomersController::class, 'register']);
    Route::post('Customers',[CustomersController::class, 'get']);
    Route::post('Customers/Authenticated',[CustomersController::class, 'getCustomer']);
    Route::post('Customers/Logout',[CustomersController::class, 'logout']);
    Route::post('Customers/Update',[CustomersController::class, 'update']);
    Route::post('Customers/Search',[CustomersController::class, 'search']);

    //Branches
    Route::post('Companies/Branches',[BranchesController::class, 'get']);
    Route::post('Companies/Branches/Search',[BranchesController::class, 'search']);
    Route::post('Companies/Branches/Register',[BranchesController::class, 'register']);
    Route::post('Companies/Branches/Update',[BranchesController::class, 'update']);

    //Places
    Route::post('Places/Regions',[PlacesController::class, 'getRegions']);
    Route::post('Places/Regions/Search',[PlacesController::class, 'searchRegions']);
    Route::post('Places/Districts',[PlacesController::class, 'getDistricts']);
    Route::post('Places/Districts/Search',[PlacesController::class, 'searchDistricts']);
    Route::post('Places/Wards',[PlacesController::class, 'getWards']);
    Route::post('Places/Wards/Search',[PlacesController::class, 'searchWards']);

    //Products
    Route::post('Products',[ProductsController::class, 'get']);
    Route::post('Products/Search',[ProductsController::class, 'search']);
    Route::post('Products/Register',[ProductsController::class, 'register']);
    Route::post('Products/Update',[ProductsController::class, 'update']);
   


});



