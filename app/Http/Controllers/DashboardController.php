<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\cheques;
use App\Models\Credit;
use App\Models\customers;
use App\Models\damages;
use App\Models\orderProducts;
use App\Models\orders;
use App\Models\posData;
use App\Models\posUsers;
use App\Models\Products;
use App\Models\Purchases;
use App\Models\supplier;
use App\Models\tables;
use App\Models\User;
use App\Models\userData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index($id)
    {
        if (Auth::check()) {
            $code = UrlToCode(sanitize($id));
            if ($code == get_Cookie("admin_session")) {
                if ($this->login($code)) {
                    return redirect("/dashboard");
                }
                return response()->view('errors.404')->setStatusCode(404);
            }
            return response()->view('errors.404')->setStatusCode(404);
        }
        return response()->view('errors.404')->setStatusCode(404);
    }

    public function home($id)
    {
        try {
            $id = Crypt::decrypt($id);
            if(tables::where('id', $id)->first() != null) {
                return view('index', ['table_id'=>$id]);
            }
        } catch (\Throwable $th) {
            return display404();
        }
        return display404();
    }

    public static function check($plan_verify = false)
    {
        return Auth::check();
    }

    public static function login($code)
    {
        $verify = posData::where('admin_id', Auth::user()->id)->get();
        if (Crypt::decrypt($code) == $verify[0]->pos_code) {
            set_Cookie('pos_session', $code);
            return true;
        }
        return response()->view('errors.404')->setStatusCode(404);
    }

    public function dashboard()
    {
        login_redirect('/dashboard');

        if (!Auth::check()) {
            return redirect('/signin');
        }

        $company = company();

        (float)$todaysales = 0;
        (float)$cost = 0;
        $sales = array();
        $yearcost = array();
        $yearexpense = array();
        $todaysales = orders::whereDate('created_at', Carbon::today())->count();
        $pendingOrders = orders::whereDate('created_at', Carbon::today())->where('status', 'pending')->count();
        $completedOrders = orders::whereDate('created_at', Carbon::today())->where('status', 'accepted')->count();
        $best_selling = DB::table('order_products')->select('*')->leftJoin('orders', 'order_products.order_id', '=', 'orders.order_number')->groupBy('sku')->orderByDesc('qty')->limit(3)->get();

        $salesqry = orders::whereYear('created_at', date('Y'))->orderBy('created_at', 'ASC')->get();

        if ($salesqry && $salesqry->count() > 0) {
            foreach ($salesqry as $key => $sale) {
                if (array_key_exists(date('M', strtotime($sale['created_at'])), $sales)) {
                    (float)$sales[date('M', strtotime($sale['created_at']))] += (float)$sale['total'] + $sale['service_charge'] - $sale['roundup'];
                } else {
                    (float)$sales[date('M', strtotime($sale['created_at']))] = 0;
                    (float)$sales[date('M', strtotime($sale['created_at']))] += (float)$sale['total'] + $sale['service_charge'] - $sale['roundup'];
                }

                if (array_key_exists(date('M', strtotime($sale['created_at'])), $yearcost)) {
                    (float)$yearcost[date('M', strtotime($sale['created_at']))] += (float)$sale['total_cost'];
                } else {
                    (float)$yearcost[date('M', strtotime($sale['created_at']))] = 0;
                    (float)$yearcost[date('M', strtotime($sale['created_at']))] += (float)$sale['total_cost'];
                }
            }
        }

        return view('pos.dashboard')->with(['company' => $company, 'todaysales' => $todaysales, 'pendingOrders' => $pendingOrders, 'completedOrders' => $completedOrders, 'cost' => $cost, 'sales' => json_encode($sales), 'yearcost' => json_encode($yearcost), 'yearexpense' => json_encode($yearexpense), 'best_sellings' => $best_selling]);
    }

    public function tables()
    {
        login_redirect('/' . request()->path());

        if (Auth::check()) {
            $tables = tables::orderBy('id', 'DESC')->get();
            return view('pos.tables')->with(['tables'=>$tables]);
        } else {
            return redirect('/signin');
        }
    }

    public function listProducts()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && $this->check(true)) {
            $products = Products::where('parent', '0')->get();
            return view('pos.list-product')->with(['products' => $products]);
        } else {
            return redirect('/signin');
        }
    }

    public function createProduct()
    {
        if (Auth::check() && $this->check(true)) {
            return view('pos.add-product');
        } else {
            return redirect('/signin');
        }
    }

    public function listCategories()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && $this->check(true)) {
            $categories = Categories::all();
            return view('pos.list-categories')->with(['categories' => $categories]);
        } else {
            return redirect('/signin');
        }
    }

    public function createCategories()
    {
        if (Auth::check() && $this->check(true)) {
            return view('pos.add-category');
        } else {
            return redirect('/signin');
        }
    }

    public function listDamages()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && isAdmin()) {
            $damages = [];
            if (isset($_GET['q'])) {
                if (sanitize($_GET['q']) != 'all') {
                    $damages = damages::where('status', sanitize($_GET['q']))->get();
                    return view('pos.list-damages')->with(['damages' => $damages]);
                } else {
                    $damages = damages::all();
                }
            } else {
                $damages = damages::where('status', 'pending')->get();
            }
            return view('pos.list-damages')->with(['damages' => $damages]);
        } else {
            return redirect('/signin');
        }
    }

    public function createDamages()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && isCashier()) {
            return view('pos.add-damage');
        } else {
            return redirect('/signin');
        }
    }

    public function salesReport()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && isCashier()) {

            $sales = orders::whereDate('created_at', Carbon::today())->orderBy('created_at', 'DESC')->get();
            $customers = customers::all();
            $pos_user = DB::table('pos_users')->select('*')->leftJoin('users', 'pos_users.user_id', '=', 'users.id')->get();

            return view('pos.sales')->with(['sales' => json_encode($sales), 'customers' => json_encode($customers), 'cahiers' => json_encode($pos_user)]);
        } else {
            return redirect('/account/overview');
        }
    }

    public function stockReport()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && isAdmin()) {

            $stock = DB::select('select *, products.qty as stock, sum(damages.qty) as damage_qty from products, damages WHERE product=sku AND status = "pending" GROUP BY product');

            return view('pos.list-stock')->with(['stocks' => $stock]);
        } else {
            return redirect('/account/overview');
        }
    }

    public function getSalesProducts(Request $request)
    {

        if (Auth::check() && $this->check(true)) {

            $sales = orderProducts::where('order_id', sanitize($request->input('params')['order_number']))->get();
            return response(json_encode($sales));
        } else {
            return response(json_encode(array("Not Logged In")));
        }
    }

    public function getSalesInvoice(Request $request)
    {
        $fromdate = sanitize($request->input('params')['fromdate']);
        $todate = sanitize($request->input('params')['todate']);
        $payment = sanitize($request->input('params')['payment']);
        $customer = sanitize($request->input('params')['customer']);
        $cashier = sanitize($request->input('params')['cashier']);

        if ($payment == '0') {
            $payment = ' WHERE ';
        } elseif (in_array($payment, ['cash', 'credit', 'card'])) {
            $payment = ' WHERE payment_method = "' . $payment . '" AND ';
        } else {
            return 0;
        }

        if ($customer == "0") {
            $customer = '';
        } elseif (customers::where('id', $customer)->get()->count() > 0) {
            $customer = ' customer = ' . $customer . ' AND ';
        } else {
            return 0;
        }

        if ($cashier == "0") {
            $cashier = ' ';
        } elseif (posUsers::where('user_id', $cashier)->get()->count() > 0) {
            $cashier = ' user_id = ' . $cashier . ' AND ';
        } else {
            return 0;
        }

        //dd(DB::select('select * from orders '.$payment.$customer.$cashier.' created_at BETWEEN "'.date('Y-m-d', strtotime($fromdate)).'" AND "'.date('Y-m-d', strtotime($todate)).'"'));
        return response(json_encode(DB::select('select * from orders ' . $payment . $customer . $cashier . ' pos_code = "' . company()->pos_code . '" AND created_at BETWEEN "' . date('Y-m-d', strtotime($fromdate)) . ' 00:00:00" AND "' . date('Y-m-d', strtotime($todate)) . ' 23:59:59"')));
    }

    public function listPurchses()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && isAdmin()) {
            $purchses = [];
            if (isset($_GET['q'])) {
                if (sanitize($_GET['q']) != 'all') {
                    $purchses = Purchases::where('status', sanitize($_GET['q']))->get();
                    return view('pos.list-purchase')->with(['purchses' => $purchses]);
                } else {
                    $purchses = Purchases::all();
                }
            } else {
                $purchses = Purchases::where('status', 'pending')->get();
            }
            return view('pos.list-purchase')->with(['purchses' => $purchses]);
        } else {
            return redirect('/signin');
        }
    }

    public function createPurchse()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && isCashier()) {
            return view('pos.add-purchase');
        } else {
            return redirect('/signin');
        }
    }

    public function listCheques()
    {
        login_redirect('/' . request()->path());

        if (Auth::check() && isAdmin()) {
            $cheques = [];
            if (isset($_GET['q'])) {
                if (sanitize($_GET['q']) != 'all') {
                    $cheques = cheques::where('status', sanitize($_GET['q']))->get();
                    return view('pos.list-cheque')->with(['cheques' => $cheques]);
                } else {
                    $cheques = cheques::all();
                }
            } else {
                $cheques = cheques::where('status', 'pending')->get();
            }
            return view('pos.list-cheque')->with(['cheques' => $cheques]);
        } else {
            return redirect('/signin');
        }
    }

    public function createCheque()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && isAdmin()) {
            $credits = Credit::where('ammount', '>', 0)->get('order_id');
            return view('pos.add-cheque', ['credits' => $credits]);
        } else {
            return redirect('/signin');
        }
    }

    public function listCustomers()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && isCashier()) {
            $customers = customers::all();
            return view('pos.list-customers')->with(['customers' => $customers]);
        } else {
            return redirect('/signin');
        }
    }

    public function createCustomer()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && isCashier()) {
            return view('pos.add-customer');
        } else {
            return redirect('/signin');
        }
    }

    public function listUsers()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && $this->check(true)) {
            $users = DB::table('users')->select('*')->leftJoin('pos_users', 'users.id', '=', 'pos_users.user_id')->get();
            return view('pos.list-users')->with(['users' => $users]);
        } else {
            return redirect('/signin');
        }
    }

    public function createUsers()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && $this->check(true)) {
            return view('pos.add-users');
        } else {
            return redirect('/signin');
        }
    }

    public function listSuppliers()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && $this->check(true)) {
            $suppliers = supplier::all();
            return view('pos.list-suppliers')->with(['suppliers' => $suppliers]);
        } else {
            return redirect('/signin');
        }
    }

    public function createSuppliers()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && $this->check(true)) {
            return view('pos.add-supplier');
        } else {
            return redirect('/signin');
        }
    }

    public function updateUser()
    {
        login_redirect('/' . request()->path());
        if (Auth::check() && $this->check()) {
            $posData = posData::first();
            $userData = userData::first();
            return view('pos.user-update')->with(['posData' => $posData, 'userData' => $userData]);
        }
    }

    public function updateUserDetails(Request $request)
    {
        if (Auth::check() && $this->check()) {
            $country = sanitize($request->input('country'));
            $address = sanitize($request->input('address'));
            $city = sanitize($request->input('city'));
            $zip = sanitize($request->input('zip'));
            $phone = sanitize($request->input('phone'));
            $imageName = "";

            if (empty($country) || empty($address) || empty($city) || empty($zip) || empty($phone)) {
                return response(json_encode(array("error" => 1, "msg" => "Please fill all fields")));
            }

            if (country($country) == false) {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Country")));
            }

            if (!is_numeric($phone)) {
                return response(json_encode(array("error" => 1, "msg" => "Please only use numbers for phone number")));
            }

            if ($request->hasFile('profile_pic')) {
                $extension = $request->file('profile_pic')->getClientOriginalExtension();
                if (in_array($extension, array('png', 'jpeg', 'jpg'))) {
                    $imageName = time() . rand(11111, 99999999) . '.' . $request->profile_pic->extension();
                    $request->profile_pic->move(public_path('assets/images/user_profile'), $imageName);
                } else {
                    return response(json_encode(array("error" => 1, "msg" => "Please select 'png', 'jpeg', or 'jpg' type image")));
                }
            }

            $userData = '';

            if ($request->hasFile('profile_pic')) {
                $userData = userData::where('user_id', Auth::user()->id)->update([
                    "country" => $country,
                    "address" => $address,
                    "city" => $city,
                    "zip" => $zip,
                    "phone" => $phone,
                    "profile" => $imageName,
                ]);
            } else {
                $userData = userData::where('user_id', Auth::user()->id)->update([
                    "country" => $country,
                    "address" => $address,
                    "city" => $city,
                    "zip" => $zip,
                    "phone" => $phone,
                ]);
            }


            if ($userData) {
                return response(json_encode(array("error" => 0, "msg" => "Details updated successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Sorry! something went wrong")));
        }
    }

    public function updateCompanyDetails(Request $request)
    {
        if (Auth::check() && $this->check()) {
            $company = sanitize($request->input('name'));
            $industry = sanitize($request->input('industry'));
            $country = sanitize($request->input('country'));
            $city = sanitize($request->input('city'));
            $currency = sanitize($request->input('currency'));

            if (empty($company) || empty($industry) || empty($country) || empty($city) || empty($currency)) {
                return response(json_encode(array("error" => 1, "msg" => "Please fill all fields")));
            }

            if (!in_array($currency, array('LKR', 'USD', 'GBP', 'EUR', 'INR'))) {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Currency")));
            }

            if (country($country) == false) {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Country")));
            }

            $userData = posData::where('admin_id', Auth::user()->id)->update([
                "company_name" => $company,
                "industry" => $industry,
                "country" => $country,
                "city" => $city,
                "currency" => $currency,
            ]);

            if ($userData) {
                return response(json_encode(array("error" => 0, "msg" => "Details updated successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Sorry! something went wrong")));
        }
    }
}
