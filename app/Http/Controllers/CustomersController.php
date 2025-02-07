<?php

namespace App\Http\Controllers;

use App\Models\customers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function getCustomers() {
        $response = [];
        if (Auth::check()) {
            return response(json_encode(customers::all()));
        }
        else {
            $response ['error'] = 1;
            $response ['msg'] = "not_logged_in";
            return response(json_encode($response));
        }
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
        if (Auth::check() && isCashier()) {
            $name = sanitize($request->input('name'));
            $address = sanitize($request->input('address'));
            $phone = sanitize($request->input('phone'));

            if (empty($name)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }
            if (!empty($phone) && !is_numeric($phone)) {
                return response(json_encode(array("error" => 1, "msg" => "Please use only numbers for phone number")));
            }

            $customer = new customers();
            $customer->name = $name;
            $customer->pos_code = company()->pos_code;
            $customer->address = $address;
            $customer->phone = $phone;
            $customer->credit_limit = 0;

            if ($customer->save()) {
                return response(json_encode(array("error" => 0, "msg" => "Customer Created Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(customers $customers)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (!Auth::check() && isCashier()) {
            return redirect('/signin');
        }

        $customer = customers::where('id', sanitize($id))->get();

        if ($customer && $customer->count() > 0) {
            return view('pos.add-customer')->with('customer', $customer[0]);
        } else {
            return display404();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, customers $customers)
    {
        if (Auth::check() && isCashier()) {
            $id = sanitize($request->input('modelid'));
            $name = sanitize($request->input('name'));
            $address = sanitize($request->input('address'));
            $phone = sanitize($request->input('phone'));

            if (empty($name)) {
                return response(json_encode(array("error" => 1, "msg" => "Please Fill All Required Fields Marked In '*'")));
            }

            if (!empty($phone) && !is_numeric($phone)) {
                return response(json_encode(array("error" => 1, "msg" => "Please use only numbers for phone number")));
            }

            $id_verify = customers::where('id', $id)->get();

            if ($id_verify && $id_verify->count() > 0) {
                # continue
            } else {
                return response(json_encode(array("error" => 1, "msg" => "Invalid Update Attempt")));
            }

            $customer = customers::where('id', $id)->update([
                "name" => $name,
                "address" => $address,
                "phone" => $phone,
                "credit_limit" => 0,
            ]);

            if ($customer) {
                return response(json_encode(array("error" => 0, "msg" => "Customer Updated Successfully")));
            }

            return response(json_encode(array("error" => 1, "msg" => "Something went wrong please, try again later")));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if (Auth::check() && isAdmin()) {
            $id = sanitize($request->input('id'));
            $verify = customers::where('id', $id);
            if ($verify && $verify->get()->count() > 0) {
                if ($verify->delete()) {
                    return response(json_encode(array("error" => 0, "msg" => "Customer deleted successfully")));
                }
                return response(json_encode(array("error" => 1, "msg" => "Customer not found")));
            }
            return response(json_encode(array("error" => 1, "msg" => "Sorry! something went wrong")));
        }
    }
}
