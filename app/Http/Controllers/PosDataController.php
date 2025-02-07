<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\orderProducts;
use App\Models\orders;
use App\Models\posData;
use App\Models\posUsers;
use App\Models\Products;
use App\Models\saveOrders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class PosDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        if (Auth::check()) {
            return redirect("/pos");
        }
        return response()->view('errors.404')->setStatusCode(404);
    }

    public static function company()
    {
        $data = posData::first();
        if ($data && $data->count() > 0) {
            return (object)$data;
        }
        return defaultValues();
    }

    public static function check($plan_verify = false)
    {
        Auth::check();
    }

    public static function login($code)
    {
        $verify = posUsers::where('user_id', Auth::user()->id)->where('pos_code', Crypt::decrypt($code))->get();
        if ($verify && $verify->count() > 0) {
            set_Cookie('pos_session', $code);
            set_Cookie('user_code', Hash::make("This is just a duplicate"));
            return true;
        }
        return response()->view('errors.404')->setStatusCode(404);
    }

    public function getPosData()
    {
        $response = [];
        if (Auth::check()) {
            $data = posData::all(['pos_code', 'plan', 'currency']);
            if ($data && $data->count() > 0) {
                return (object)$data[0];
            }
            return defaultValues();
        } else {
            $response['error'] = 1;
            $response['msg'] = "not_logged_in";
            return response(json_encode($response));
        }
    }

    public function checkout(Request $request)
    {
        $products = filter_var_array($request->input('products'), FILTER_SANITIZE_STRING);
        $order_id = orders::orderBy('id', 'DESC')->first();
        $order_id = $order_id &&  $order_id->count() > 0 ? (int)$order_id->order_number + 1 : 1001;
        $total = 0;

        foreach ($products as $product) {
            $pro = Products::where('sku', $product['sku'])->sum('price');
            $total += $pro*$product['qty'];
        }

        if (count($products) > 0) {
            $order = new orders();
            $order->order_number = $order_id;
            $order->pos_code = '';
            $order->user_id = 0;
            $order->order_table = sanitize($request->input('table_id'));
            $order->customer = json_encode(['name'=>sanitize($request->input('customer_name')), 'phone'=>sanitize($request->input('customer_phone')), 'email'=>sanitize($request->input('customer_email'))]);
            $order->total = $total;
            $order->total_cost = 0;
            $order->service_charge = 0;
            $order->roundup = 0;
            $order->payment_method = in_array(sanitize($request->input('payment_method')), ['Cash', 'Card'])?sanitize($request->input('payment_method')): 'Cash';
            $order->invoice = "";
            $order->payment_status = "pending";
            $order->status = "pending";

            if ($order->save()) {
                foreach ($products as $product) {
                    $temp = Products::where('sku', $product['sku']);
                    if ($temp && $temp->get()->count() > 0) {
                        $temp->update(['qty' => (float)$temp->get()[0]->qty - (float)$product['qty']]);
                    }
                    orderProducts::insert([
                        "order_id" => $order_id,
                        "pro_name" => $product['pro_name'],
                        "sku" => $product['sku'],
                        "qty" => $product['qty'],
                        "price" => $product['price'],
                        "cost" => 0,
                        "discount" => 0,
                        "discount_mod" => 0,
                        "discounted_price" => 0,
                        "pos_id" => '',
                    ]);
                }

                $inName = str_replace(' ', '-', str_replace('.', '-', $order_id)) . '-Invoice-' . date('d-m-Y-h-i-s') . '-' . rand(0, 999) . '.pdf';

                $generate_invoice = generateThermalInvoice($order_id, 0, $inName);

                if ($generate_invoice->generated == true) {
                    orders::where('order_number', $order_id)->update([
                        "invoice" => $inName,
                    ]);

                    $order_data = get_Cookie('orders');
                    if ($order_data != false && count(json_decode($order_data)) > 0) {
                        $order_data = json_decode($order_data);
                        $order_data[] = $order_id;
                    }
                    else {
                        $order_data[] = $order_id;
                    }

                    set_Cookie('orders', json_encode($order_data));

                    return response(json_encode(array("error" => 0, "msg" => "Checkout successful", "url" => $inName, 'order_id'=>$order_id)));
                } else {
                    return response(json_encode(array("error" => 0, "msg" => "Checkout successful, Couldn't print invoice: " + $generate_invoice->msg, 'order_id'=>$order_id)));
                }
            } else {
                return response(json_encode(array("error" => 1, "msg" => "Error while proceeding, please try again later")));
            }
        }

        return response(json_encode(array("error" => 1, "msg" => "Error while proceeding, please try again later")));
    }

    public function save(Request $request)
    {
        if (Auth::check() && Auth::check()) {
            $order = new saveOrders();
            $order->products = json_encode($request->input('params')['products']);
            $order->order_name = sanitize($request->input('params')['order_name']);
            $order->user_id = Auth::user()->id;
            $order->pos_code = $this->company()->pos_code;
            if ($order->save()) {
                return response(json_encode(array("error" => 0, "msg" => "Products saved successfully")));
            } else {
                return response(json_encode(array("error" => 1, "msg" => "Error while saving, please try again later")));
            }
        }

        return response(json_encode(array("error" => 1, "msg" => "Not logged in")));
    }

    public function getSavedOrders(Request $request)
    {
        if (Auth::check() && Auth::check()) {
            if ($request->exists('params')) {
                $orders = saveOrders::where('pos_code', $this->company()->pos_code)->where('id', $request->input('params')['id'])->get();
                return response(json_encode($orders));
            } else {
                $orders = saveOrders::where('pos_code', $this->company()->pos_code)->get();
                return response(json_encode($orders));
            }
        }
        return response(json_encode(array("error" => 1, "msg" => "Not logged in")));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        login_redirect('/pos');
        if (Auth::check()) {
            return view("pos.pos");
        } else {
            return redirect('/signin');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(posData $posData)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, posData $posData)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(posData $posData)
    {
        //
    }
}
