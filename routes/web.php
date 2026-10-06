<?php

use App\Http\Controllers\API\Hotel\HotelmanagementController;
use App\Http\Controllers\Branches\BranchesController;
use App\Http\Controllers\Directorates\DirectoratesController;
use App\Http\Controllers\Directorates\SectionsController;
use App\Http\Controllers\Hrms\EmployeesController;
use App\Http\Controllers\Stock\CategoriesController;
use App\Http\Controllers\Stock\CustomersController;
use App\Http\Controllers\Stock\InvoiceController;
use App\Http\Controllers\Stock\LogisticsController;

use App\Http\Controllers\WhatsAppGatewayController;



use App\Http\Controllers\Stock\ProductsController;
use App\Http\Controllers\Stock\StoresController;
use App\Http\Controllers\Stock\DailyExpensesController;
use App\Http\Controllers\Stock\CustomerStatementController;
use App\Http\Controllers\Stock\BankDepositSlipController;
use App\Http\Controllers\Stock\DayClosureController;
use App\Http\Controllers\Stock\TripLocationController;
use App\Http\Controllers\Tuli\TuliStoresController;
use App\Http\Controllers\Tuli\inventoryManagentController;
use App\Http\Controllers\Tuli\CategoriesController as TuliCategoriesController;


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\PortalUsersController;


// admin_web.php was unused admin-theme demo routes (ui-kits, ecommerce
// templates, etc.) referenced only by unused alternate layouts
// (modern-layout/compact-layout/default-layout). Quarantined to
// _legacy/routes/admin_web.php per ammendments.md §1/§4 — not included.
// passwordHash


//Portal Users Auth
Route::get('/', [PortalUsersController::class, 'index'])->name('/');
Route::get('/date', function () {
    return view('date');
});
Route::post('/portal/auth', [PortalUsersController::class, 'loginWeb']);

Route::get('/portal/auth', [PortalUsersController::class, 'index'])->name('login');

Route::post('/forget-password', function(){
    return 'Upcoming Soon !';
})->name('forget-password');

Route::get('/how-to-use', [PortalUsersController::class, 'howToUse'])->name('how-to-use');
// Public company website (must stay above the '/{id}' catch-all)
Route::get('/website', function () {
    return view('website', ['company' => \App\Models\Company::find(1)]);
})->name('website');
Route::get('/{id}',[InvoiceController::class, 'download'])->name('free-quotation-download');
Route::get('/l/{id}',[LogisticsController::class, 'download'])->name('free-ledger-download');
// Customer statement opened from the QR code on a printed payment reminder (signed, no login)
Route::get('/s/{id}', [CustomerStatementController::class, 'publicPdf'])->middleware('signed')->name('customer-statement-public');

Route::group(['prefix' => 'v1/','middleware' => ['auth']], function()
{
    Route::get('logout',[PortalUsersController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [PortalUsersController::class, 'indexTest'])->name('home');

    
    
    Route::view('dashboard-overview', 'admin.dashboard.overview')->name('home-overview');
    Route::view('dashboard-hotel', 'admin.dashboard.home-hotel')->name('home-hotel');
    Route::view('dashboard-truck', 'admin.dashboard.home-truck')->name('home-truck');
    Route::view('dashboard-hardcore', 'admin.dashboard.home-hardware')->name('home-hardcore');
    Route::view('summary', 'admin.dashboard.general_summary')->name('general');
    // my routes
   

    //employess managemnt
    Route::get('employees/management', [EmployeesController::class, 'get'])->name('employees-management');
    Route::get('properties/staff/registration/profile/{id}',[EmployeesController::class, 'profile'])->name('staff-profile');
    Route::get('leave/management', [EmployeesController::class, 'leaveAdministration'])->name('leave-management');
    Route::post('add/leave', [EmployeesController::class, 'registerLeave'])->name('add-leave');

    //directorate managemnt
    Route::get('directorates/management', [DirectoratesController ::class, 'get'])->name('directorates-management');
    Route::post('add/directorates', [DirectoratesController::class, 'register'])->name('add-directorates');

    //hotel managemnt
    Route::get('hotel/management', [HotelmanagementController ::class, 'get'])->name('hotel-management');
    Route::post('add/hotel', [HotelmanagementController::class, 'register'])->name('add-hotel');

    Route::get('room/category/management', [HotelmanagementController::class, 'getRoomCategories'])->name('room-category-management');
    Route::get('room/management', [HotelmanagementController::class, 'getRooms'])->name('room-management');
    Route::post('add/room/category', [HotelmanagementController::class, 'registerRoomCategories'])->name('add-room-category');
    Route::post('add/room', [HotelmanagementController::class, 'registerRoom'])->name('add-room');
    Route::get('mycustomers/management', [HotelmanagementController::class, 'getMyCustomers'])->name('mycustomers-management');
    Route::post('add/customer/hotel', [HotelmanagementController::class, 'registerHotelCustomer'])->name('add-customer-hotel');
    Route::get('properties/customer/hotel/profile/{id}',[HotelmanagementController::class, 'HotelCustomerProfile'])->name('customer-hotel-profile');

    //section managemnt
    Route::get('section/management', [SectionsController::class, 'get'])->name('section-management');
    Route::post('add/section', [SectionsController::class, 'register'])->name('add-section');

    //branches managemnt 
    Route::get('branches/management', [BranchesController::class, 'get'])->name('branches-management');
    Route::post('add/branch', [BranchesController::class, 'register'])->name('add-branch');

    //branches managemnt 
    Route::get('stores/management', [StoresController::class, 'get'])->name('stores-management');
    Route::post('add/stores', [StoresController::class, 'register'])->name('add-stores');
    Route::post('add/stores/tuli', [TuliStoresController::class, 'register'])->name('add-stores-tuli');
    Route::get('store/status/update/{id}/{status}', [TuliStoresController::class, 'updateUserStatus'])->name('store-status-update');


    Route::get('stores/management/tuli', [TuliStoresController::class, 'get'])->name('stores-management-tuli');

    //branches managemnt 
    Route::get('categories/management', [CategoriesController::class, 'get'])->name('categories-management');
    Route::get('categories/management/tuli', [TuliCategoriesController::class, 'get'])->name('categories-management-tuli');
    Route::post('add/categories/tuli', [TuliCategoriesController::class, 'register'])->name('add-categories-tuli');
    Route::get('category/status/update/{id}/{status}', [TuliCategoriesController::class, 'updateCategoryStatus'])->name('category-status-update');
    Route::get('invetories/management/tuli', [TuliCategoriesController::class, 'getInventories'])->name('invetories-management-tuli');
    Route::post('add/inventory/tuli', [TuliCategoriesController::class, 'registerInvetories'])->name('add-inventory-tuli');
    Route::get('inventory/preview/tuli/{id}',[inventoryManagentController::class, 'InventoryPreview'])->name('inventory-preview-tuli');
    Route::post('record/expense/tuli', [inventoryManagentController::class, 'registerNewExpenses'])->name('record-expense-tuli');
    Route::post('add/product/inventory/tuli', [inventoryManagentController::class, 'registerInventory'])->name('add-product-inventory-tuli');
    Route::get('send/stock/tuli/{id}', [inventoryManagentController::class, 'sendProductsTostock'])->name('send-stock-tuli');
    Route::post('add/categories', [CategoriesController::class, 'register'])->name('add-categories');
    Route::get('my/suppliers/tuli', [TuliCategoriesController::class, 'getmySuppliers'])->name('my-suppliers-tuli');
    Route::post('my/suppliers/tuli', [TuliCategoriesController::class, 'getmySuppliers'])->name('my-suppliers-tuli');
    Route::get('expenses/management/tuli', [TuliCategoriesController::class, 'getExpensies'])->name('expenses-management-tuli');
    Route::post('add/expense/tuli', [TuliCategoriesController::class, 'registerExpenses'])->name('add-expense-tuli');
    Route::get('product/registration/tuli', [inventoryManagentController::class, 'getAllProducts'])->name('product-registration-tuli');
    Route::get('customers/management/tuli', [inventoryManagentController::class, 'getAllCustomers'])->name('customers-management-tuli');
    Route::post('add/customer/tuli', [inventoryManagentController::class, 'registerCustomer'])->name('add-customer-tuli');
    Route::get('/customer/profile/tuli/{id}',[inventoryManagentController::class, 'profile'])->name('customer-profile-tuli');
    Route::get('tuli/invoice/status/update/{id}',[inventoryManagentController::class, 'invoiceStatusUpdate'])->name('invoice-status-update-tuli');
    Route::post('edit/tuli/invoice/price', [inventoryManagentController::class, 'invoiceStatusUpdatePaid'])->name('receive-invoice-payment-tuli');
    Route::get('sales/invoice/preview/tuli/{id}',[inventoryManagentController::class, 'invoicePreviewTuli'])->name('invoice-preview-tuli');
    Route::post('edit/item/invoice/qty/tuli', [inventoryManagentController::class, 'EdititemInvoiceQty'])->name('edit-item-invoice-qty-tuli');
    Route::post('edit/item/invoice/tuli', [inventoryManagentController::class, 'EdititemInvoice'])->name('edit-item-invoice-price-tuli');
    Route::get('sales/report/tuli', [inventoryManagentController::class, 'salesReport'])->name('sales-report-tuli');

    Route::get('/quick-sale', function () {
        return view('admin.tuli_sales_management.quick-sale');
    })->name('quick-sale');
    Route::view('/quick-sale/mbao', 'admin.sales_management.quick-sale')->name('quick-sale-mbao');

    Route::post('add/inventory', [CategoriesController::class, 'registerInvetories'])->name('add-inventory');
    Route::post('add/expense', [CategoriesController::class, 'registerExpenses'])->name('add-expense');

    Route::get('invetories/management', [CategoriesController::class, 'getInventories'])->name('invetories-management');
    Route::get('expenses/management', [CategoriesController::class, 'getExpensies'])->name('expenses-management');

    // Mbao daily (running) expenses — separate from inventory expenses
    // Customer statement of unpaid invoices (screen + PDF reminder)
    Route::get('customers/{id}/statement', [CustomerStatementController::class, 'show'])->name('customer-statement');
    Route::get('customers/{id}/statement/pdf', [CustomerStatementController::class, 'pdf'])->name('customer-statement-pdf');

    Route::get('expenses/daily', [DailyExpensesController::class, 'index'])->name('daily-expenses');
    Route::post('expenses/daily', [DailyExpensesController::class, 'store'])->name('daily-expenses-store');
    Route::post('expenses/daily/types', [DailyExpensesController::class, 'storeType'])->name('daily-expense-types-store');
    Route::post('expenses/daily/{id}', [DailyExpensesController::class, 'update'])->name('daily-expenses-update');
    Route::post('expenses/daily/{id}/delete', [DailyExpensesController::class, 'destroy'])->name('daily-expenses-delete');

   
    
   

    Route::get('my/suppliers', [CategoriesController::class, 'getmySuppliers'])->name('my-suppliers');
    Route::post('my/suppliers', [CategoriesController::class, 'getmySuppliers'])->name('my-suppliers');

    Route::get('bank/deposit', [LogisticsController::class, 'getBankDeposit'])->name('bank-deposit');
    Route::post('/bank-deposits/store', [LogisticsController::class, 'storeBankDeposit'])->name('bank-deposits.store');
    Route::get('bank-deposits/slips/{id}', [BankDepositSlipController::class, 'show'])->name('bank-deposit-slip');
    Route::get('day-closures/{id}/pdf', [DayClosureController::class, 'pdf'])->name('day-closure-pdf');

    
    
    Route::get('trips/management', [LogisticsController::class, 'getTriproutes'])->name('trips-management');
    Route::post('/add-truck-route', [LogisticsController::class, 'saveTriproutes'])->name('add-truck-route');
    Route::get('/generate-trip-no', [LogisticsController::class, 'generateTripNoAjax'])->name('generate-trip-no');

    Route::get('truck/reports', [LogisticsController::class, 'getTriproutesReports'])->name('truck-reports');
    // truck-ejy/truck-erw are per-vehicle routes hardcoded to plates
    // T821EJY/T343ERW (see sidebar.blade.php). Adding a third truck means
    // adding a third route here — not parameterized in this pass
    // (ammendments.md §4) to avoid touching sidebar route names.
    Route::get('truck/ejy', [LogisticsController::class, 'getTriproutesReports'])->name('truck-ejy');
    Route::get('truck/erw', [LogisticsController::class, 'getTriproutesReports'])->name('truck-erw');

    // truck-reports

    Route::get('truck/drivers', [LogisticsController::class, 'get'])->name('truck-drivers');
    Route::post('/trucks/add', [LogisticsController::class, 'saveTruck'])->name('add-truck');
    Route::post('record/expense/truck', [LogisticsController::class, 'registerNewExpensesTruck'])->name('record-expense-truck');
    Route::post('/record-route-plan', [LogisticsController::class, 'saveReoutePlan'])->name('record-route-plan');
    Route::get('/delete-unsubmited-route/{id}/{status}', [LogisticsController::class, 'deleteUnsubmittedRoute'])->name('delete-unsubmited-route');
    Route::get('/delete-unsubmited-plam/{id}/{status}', [LogisticsController::class, 'deleteUnsubmittedRoutePlan'])->name('delete-unsubmited-plam');
    Route::get('/delete-unsubmited-expense/{id}/{status}', [LogisticsController::class, 'deleteUnsubmittedExpenseTrip'])->name('delete-unsubmited-expense');
    Route::get('send/approve/{id}', [LogisticsController::class, 'sendProductsToApprove'])->name('send-approve');
    Route::get('ledger/invoice/download/{id}',[LogisticsController::class, 'download'])->name('invoice-download-ledger');

    Route::get('send/approve/deposit/{id}', [LogisticsController::class, 'sendProductsToApproveDeposit'])->name('send-approve-deposit');

    Route::get('/delete-unsubmited-deposit/{id}/{status}', [LogisticsController::class, 'deleteUnsubmittedDeposit'])->name('delete-unsubmited-deposit');

    

    Route::get('route/preview/{id}',[LogisticsController::class, 'RoutePreview'])->name('route-preview');
    Route::post('trips/{id}/location', [TripLocationController::class, 'store'])->name('trip-location-store');
    Route::get('trips/map', [TripLocationController::class, 'map'])->name('trips-map');

    
    //customer managemnt
    // Same route name on both verbs is intentional: GET renders the list,
    // POST (from the on-page search form) re-renders it filtered. Do not
    // "deduplicate" — Blade's route('customers-management') calls assume
    // this pair exists.
    Route::get('customers/management', [CustomersController::class, 'get'])->name('customers-management');
    Route::post('customers/management', [CustomersController::class, 'searchCustomer'])->name('customers-management');
    Route::post('add/customer', [CustomersController::class, 'register'])->name('add-customer');
    Route::get('properties/customer/registration/profile/{id}',[CustomersController::class, 'profile'])->name('customer-profile');
    Route::get('invoices/pending', [CustomersController::class, 'invoiceReport'])->name('invoices-pending');
    Route::get('invoices/confermed', [CustomersController::class, 'invoiceReport'])->name('invoices-confermed');
    Route::get('invoices/paid', [CustomersController::class, 'invoiceReport'])->name('invoices-paid');
    Route::get('sales/report', [CustomersController::class, 'salesReport'])->name('sales-report');

  
    
    Route::get('/download-invoices', [CustomersController::class, 'downloadInvoicesCSV'])->name('download-invoices');
    Route::get('invoices', [CustomersController::class, 'index'])->name('invoices.index');

    Route::get('/invoices/pending', [InvoiceController::class, 'pendingInvoices'])->name('invoices');
    Route::get('/download-invoices-csv', [InvoiceController::class, 'downloadCSV'])->name('download-invoices-csv');

    

   
    Route::get('sales/invoice/download/{id}',[InvoiceController::class, 'download'])->name('invoice-download');

    Route::get('pending/invoice/download/{id}',[InvoiceController::class, 'printPendingInvoicesPdf'])->name('pending-invoice-download');
  
    Route::get('hotel/invoice/download/{id}',[InvoiceController::class, 'downloadHotelInvoice'])->name('hotel-invoice-download');
    Route::get('sales/invoice/preview/{id}',[InvoiceController::class, 'invoicePreview'])->name('invoice-preview');
    Route::get('sales/invoice/hotel/preview/{id}',[InvoiceController::class, 'invoicePreviewHotel'])->name('hotel-invoice-preview');
    Route::get('sales/invoice/status/update/{id}',[InvoiceController::class, 'invoiceStatusUpdate'])->name('invoice-status-update');
    Route::get('hotel/invoice/status/update/{id}',[InvoiceController::class, 'invoiceStatusUpdateHotel'])->name('hotel-invoice-status-update');
    Route::get('hotel/invoice/status/paid/{id}',[InvoiceController::class, 'invoiceStatusUpdatePaidHotel'])->name('hotel-invoice-status-paid');
    Route::get('sales/invoice/status/paid/{id}',[InvoiceController::class, 'invoiceStatusUpdatePaid'])->name('invoice-status-paid');

    Route::post('edit/item/invoice/price2', [InvoiceController::class, 'EdititemInvoice'])->name('edit-item-invoice-price');

    Route::post('edit/item/invoice/price', [InvoiceController::class, 'invoiceStatusUpdatePaid'])->name('receive-invoice-payment');
 

    Route::post('edit/user/store/', [InvoiceController::class, 'editUserStore'])->name('edit-user-store');
    Route::post('remove/item/from/invoice', [InvoiceController::class, 'removeItemFromInvoice'])->name('remove-item-from-invoice');
    Route::post('edit/item/invoice/qty', [InvoiceController::class, 'EdititemInvoiceQty'])->name('edit-item-invoice-qty');
    Route::post('edit/dates/invoice/hotel', [InvoiceController::class, 'EditDateInvoiceHotel'])->name('edit-dates-invoice-hotel');
    Route::post('edit/hotel/invoice/price', [InvoiceController::class, 'EdititemInvoiceHotel'])->name('edit-hotel-invoice-price');
    
    
    Route::get('/delete-unsubmited-invetory/{id}/{status}', [ProductsController::class, 'deleteUnsubmittedInventory'])->name('delete-unsubmited-invetory');
    Route::get('/delete-unsubmited-product/{id}/{status}', [ProductsController::class, 'deleteUnsubmittedProduct'])->name('delete-unsubmited-product');
    
    
    //product managemnt 
    Route::get('product/registration', [ProductsController::class, 'get'])->name('product-registration');
    Route::post('product/registration', [ProductsController::class, 'searchProduct'])->name('product-registration');
    Route::get('product/edited', [ProductsController::class, 'getEditedProduct'])->name('product-edited');
    Route::get('product/transfered', [ProductsController::class, 'getTransferedProduct'])->name('product-transfered');
    Route::get('product/preview/{id}',[ProductsController::class, 'productPreview'])->name('product-preview');
    Route::get('category/preview/{id}',[ProductsController::class, 'categoryPreview'])->name('category-preview');
    Route::get('inventory/preview/{id}',[ProductsController::class, 'InventoryPreview'])->name('inventory-preview');
    Route::get('supplier/preview/{name}',[ProductsController::class, 'SupplierPreview'])->name('supplier-preview');
    Route::get('store/preview/{id}',[ProductsController::class, 'storePreview'])->name('store-preview');
    Route::post('edit/item/product/details', [ProductsController::class, 'EditProduct'])->name('edit-item-product-details');
    Route::post('transfer/item/product/details', [ProductsController::class, 'TransferProduct'])->name('transfer-item-product-details');
    Route::get('edited/product/adminidtration/{id}/{status}', [ProductsController::class, 'updateProductEditedStatus'])->name('edited-product-adminidtration');
    Route::get('send/stock/{id}', [ProductsController::class, 'sendProductsTostock'])->name('send-stock');
    Route::get('transfer/product/adminidtration/{id}/{status}', [ProductsController::class, 'updateProductTransferStatus'])->name('transfer-product-adminidtration');
    Route::post('add/product', [ProductsController::class, 'register'])->name('add-product');
    Route::post('add/product/inventory', [ProductsController::class, 'registerInventory'])->name('add-product-inventory');
    


    Route::post('record/expense', [ProductsController::class, 'registerNewExpenses'])->name('record-expense');
    
    Route::get('operate/sale', [ProductsController::class, 'oparateSale'])->name('operate-sale');

    Route::get('/import-excel', [ProductsController::class, 'index'])->name('import.excel');
    Route::post('/import-excel', [ProductsController::class, 'import']);


    //Security
    Route::get('security/users', [PortalUsersController::class, 'get'])->name('portal-users');
    Route::post('security/users', [PortalUsersController::class, 'search'])->name('portal-users-search');
    // Route::post('loans/aplications/search', [loanController::class, 'search'])->name('loans-applications-search');
    Route::get('security/users/profile', [PortalUsersController::class, 'profile'])->name('security-user-profile');
    Route::get('security/user/profile/manage/{id}/{status}', [DirectoratesController::class, 'updateUserStatus'])->name('user-status-update');
    Route::get('security/section/profile/manage/{id}/{status}', [SectionsController::class, 'updateUserStatus'])->name('section-status-update');
    Route::get('security/branch/profile/manage/{id}/{status}', [BranchesController::class, 'updateUserStatus'])->name('branch-status-update');
    
   
    Route::post('security/users/profile/change_password', [PortalUsersController::class, 'change_password'])->name('security-user-change_password');
    Route::post('security/users/add', [PortalUsersController::class, 'register'])->name('portal-users-add');
    Route::get('security/user/status/manage/{id}/{status}', [PortalUsersController::class, 'updateStatus'])->name('portal-user-status-update');
    Route::post('security/users/{id}/role', [PortalUsersController::class, 'updateRole'])->name('portal-users-update-role');
    Route::view('security/configurations', 'admin.security.configurations')->name('security-system-configurations');
    Route::view('security/configurations', 'admin.security.configurations')->name('security-system-configurations');
    Route::view('security/audit', 'admin.security.audit')->name('security-system-audit-trail');

     //Products
    Route::view('products/reports', 'admin.products.reports')->name('products-reports'); 
   
    Route::get('security/product/profile/manage/{id}/{status}', [PortalUsersController::class, 'updateProdyctStatus'])->name('product-status-update');

       //Properties
    Route::view('properties/reports', 'admin.properties.reports')->name('properties-reports');
    Route::get('customers/registration', [CustomersController::class, 'get'])->name('customers-registration');

   
    

});
