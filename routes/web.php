<?php

use App\Http\Controllers\accountController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ChequesController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\DamagesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PosDataController;
use App\Http\Controllers\PosInvitationController;
use App\Http\Controllers\pricePlan;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\PurchasesController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TablesController;
use App\Http\Controllers\UserDataController;
use App\Models\Categories;
use App\Models\orderProducts;
use App\Models\orders;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
// Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
// Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
// Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('/table/{table_id}', [DashboardController::class, 'home']);

Route::get('/check', function () {
    dd(get_Cookie('orders'));
});

//Route::post('/contact', [ContactController::class, 'create'])->name('contact');

// Route::get('/create-account', [UserDataController::class, 'index']);

//Route::get('/pricing', function () {
//    login_redirect('/pricing');
//    return view('pricing');
//});
//
//Route::get('/pricing/{plan}', [pricePlan::class, 'index']);
//
//Route::get('/privacy', function () {
//    login_redirect('/privacy');
//    return view('privacy');
//});

//============= POS Routes ==========////

Route::get('/pos', [PosDataController::class, 'show']);
Route::post('/pos/get_orders', [OrdersController::class, 'getOrders']);
Route::post('/pos/update_orders', [OrdersController::class, 'updateOrder']);


Route::post('/get_categories', function () {
    return response(json_encode(Categories::all(['category_name', 'image', 'id'])));
});

Route::post('/checkout', [PosDataController::class, 'checkout']);

Route::post('/get_products', function (Request $request) {
    $products = Products::where('category', sanitize($request->input('category')))->where('parent', '0')->where('qty', '>', '0')->get(['id', 'sku', 'pro_name', 'price', 'category']);
    foreach ($products as $key => $pro) {
        $variant = Products::where('parent', $pro->id)->where('qty', '>', '0')->get(['id', 'sku', 'pro_name', 'price']);
        $pro['variants'] = $variant->map(function ($item) {
            return $item->attributesToArray();
        });
        if ($variant->count() > 0) {
            $products[$key]['has_variant'] = true;
        } else {
            $products[$key]['has_variant'] = false;
        }
        $products[$key] = $pro;
    }
    return response(json_encode($products));
});

Route::post('/get_orders', function (Request $request) {
    $orders = get_cookie('orders');
    if ($orders == null) {
        return response(json_encode([]));
    }

    $orderData = [];

    foreach (json_decode($orders) as $key => $id) {
        $order = orders::where('order_number', $id)->first(['order_number', 'total', 'status']);

        if ($order != null) {
            $pro = orderProducts::where('order_id', $order->order_number)->get(['pro_name', 'sku', 'qty', 'price']);
            if ($pro != null) {
                $order['products'] = $pro->map(function ($item) {
                    return $item->attributesToArray();
                });
            }

            $orderData[] = $order;
        }
    }

    return response(json_encode($orderData));
});


//============= Dashboard Routes ==========////

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

Route::prefix('dashboard')->group(function () {
    Route::get('tables', [DashboardController::class, 'tables']);
    Route::post('table/create', [TablesController::class, 'store']);
    Route::post('tables/update', [TablesController::class, 'update']);
    Route::delete('tables/delete', [TablesController::class, 'destroy']);

    Route::get('products', [DashboardController::class, 'listProducts']);
    Route::get('products/create', [DashboardController::class, 'createProduct']);
    Route::get('products/edit/{id}', [ProductsController::class, 'edit']);
    Route::post('variants/create', [ProductsController::class, 'createVariant']);
    Route::post('variants/update', [ProductsController::class, 'updateVariant']);
    Route::post('products/create', [ProductsController::class, 'store']);
    Route::post('/products/edit', [ProductsController::class, 'update']);
    Route::delete('/products/delete', [ProductsController::class, 'destroy']);

    Route::get('categories', [DashboardController::class, 'listCategories']);
    Route::get('category/create', [DashboardController::class, 'createCategories']);
    Route::post('category/create', [CategoriesController::class, 'store']);
    Route::get('/category/edit/{id}', [CategoriesController::class, 'edit']);
    Route::post('/category/edit', [CategoriesController::class, 'update']);
    Route::delete('/categories/delete', [CategoriesController::class, 'destroy']);

    // Route::get('returns', [DashboardController::class, 'listPurchses']);
    // Route::get('returns/create', [DashboardController::class, 'createPurchse']);
    // Route::post('returns/create', [PurchasesController::class, 'store']);
    // Route::get('returns/edit/{id}', [PurchasesController::class, 'edit']);
    // Route::post('/returns/edit', [PurchasesController::class, 'update']);

    Route::get('customers', [DashboardController::class, 'listCustomers']);
    Route::get('customer/create', [DashboardController::class, 'createCustomer']);
    Route::post('customer/create', [CustomersController::class, 'store']);
    Route::get('customer/edit/{id}', [CustomersController::class, 'edit']);
    Route::post('/customer/edit', [CustomersController::class, 'update']);
    Route::delete('/customers/delete', [CustomersController::class, 'destroy']);

    Route::get('users', [DashboardController::class, 'listUsers']);
    Route::get('users/create', [DashboardController::class, 'createUsers']);
    Route::post('user/create', [UserDataController::class, 'save']);
    Route::post('user/invite', [UserDataController::class, 'invite']);
    Route::get('user/edit/{id}', [UserDataController::class, 'edit']);
    Route::post('/user/edit', [UserDataController::class, 'update']);
    Route::delete('/user/delete', [UserDataController::class, 'destroy']);

    Route::get('user/update', [DashboardController::class, 'updateUser']);
    Route::post('user/update', [DashboardController::class, 'updateUserDetails']);
    Route::post('company/update', [DashboardController::class, 'updateCompanyDetails']);
});

Route::get('/signin', function () {
    if (Auth::check()) {
        return redirect('/');
    }
    return view('auth.login');
});

Route::get('/signup', function () {
    return view('auth.register');
});

Route::post('/signup', [RegisterController::class, 'register'])->name('signup');

Route::post('/create-account', [UserDataController::class, 'store']);

Route::post('/signin', [LoginController::class, 'userLogin'])->name('userLogin');
