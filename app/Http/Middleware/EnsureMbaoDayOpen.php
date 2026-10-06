<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Stock\CategoriesController;
use App\Http\Controllers\Stock\CustomersController;
use App\Http\Controllers\Stock\DailyExpensesController;
use App\Http\Controllers\Stock\InvoiceController;
use App\Http\Controllers\Stock\ProductsController;
use App\Http\Controllers\Stock\StoresController;
use App\Models\DayClosure;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * After Mbao "End Day", Mbao users are view-only until 06:00 (see DayClosure).
 * Blocks every Mbao action that changes data — forms, the GET links that
 * change status/delete, and Livewire actions on the Mbao sales screens and
 * dashboard — while still allowing pages, lists, searches and PDFs.
 */
class EnsureMbaoDayOpen
{
    // Controllers that belong to the Mbao module.
    const MBAO_CONTROLLERS = [
        ProductsController::class,
        CategoriesController::class,
        CustomersController::class,
        InvoiceController::class,
        StoresController::class,
        DailyExpensesController::class,
    ];

    // POST routes on those controllers that only search (read-only).
    const READ_ONLY_POSTS = ['product-registration', 'customers-management', 'my-suppliers'];

    // GET routes on those controllers that change data when clicked.
    const WRITING_GETS = [
        'invoice-status-update', 'invoice-status-paid',
        'hotel-invoice-status-update', 'hotel-invoice-status-paid',
        'delete-unsubmited-invetory', 'delete-unsubmited-product',
        'edited-product-adminidtration', 'transfer-product-adminidtration',
        'send-stock',
    ];

    // Livewire components: every action blocked, or only the listed methods.
    const LIVEWIRE_BLOCKED = [
        'sales-management.quick-sale' => '*',
        'sales-management.oparate-sales' => '*',
        'components.reports.summary' => ['openDeposit', 'saveDeposit', 'previewEndDay', 'confirmEndDay', 'reopenDay'],
    ];

    public function handle(Request $request, Closure $next)
    {
        if (!$this->changesMbaoData($request)) {
            return $next($request);
        }

        $lock = DayClosure::lockFor(Auth::user());

        if (!$lock) {
            return $next($request);
        }

        $message = 'Mbao day was closed at '.$lock->closed_at->format('H:i').' — view only until '
            .$lock->locked_until->format('d M Y H:i').'. / Siku imefungwa — unaweza kuangalia tu hadi saa 12 asubuhi.';

        if ($request->is('livewire/*') || $request->expectsJson()) {
            return response($message, 423);
        }

        return redirect()->back()->with('error', $message)->withErrors(['day_closed' => $message]);
    }

    private function changesMbaoData(Request $request)
    {
        if ($request->is('livewire/message/*')) {
            return $this->livewireCallIsBlocked($request);
        }

        $route = $request->route();
        if (!$route || !in_array($this->controllerOf($route), self::MBAO_CONTROLLERS, true)) {
            return false;
        }

        $name = $route->getName();

        if ($request->isMethod('GET') || $request->isMethod('HEAD')) {
            return in_array($name, self::WRITING_GETS, true);
        }

        return !in_array($name, self::READ_ONLY_POSTS, true);
    }

    // Controller class of a route; null for closure / view routes (which have
    // no controller - Route::getControllerClass() crashes on closures in Laravel 9).
    private function controllerOf($route)
    {
        $controller = $route->getAction('controller');

        return is_string($controller) ? explode('@', $controller)[0] : null;
    }

    private function livewireCallIsBlocked(Request $request)
    {
        $component = $request->input('fingerprint.name');
        $blocked = self::LIVEWIRE_BLOCKED[$component] ?? null;

        if ($blocked === null) {
            return false;
        }

        if ($blocked === '*') {
            return true;
        }

        foreach ((array) $request->input('updates', []) as $update) {
            if (($update['type'] ?? null) === 'callMethod' && in_array($update['payload']['method'] ?? null, $blocked, true)) {
                return true;
            }
        }

        return false;
    }
}
