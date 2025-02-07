<?php

namespace App\Http\Controllers;

use App\Models\orderProducts;
use App\Models\orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function getOrders()
    {
        if (Auth::check()) {
            $orders = orders::where('status', 'pending')->get();
            foreach ($orders as $key => $value) {
                $products = orderProducts::where('order_id', $value->order_number)->get();
                $value['products'] = $products->map(function ($item) {
                    return $item->attributesToArray();
                });
                $orders[$key] = $value;
            }

            return response(json_encode($orders));
        }
    }

    public function updateOrder(Request $request)
    {
        if (Auth::check()) {
            $order = orders::where('id', sanitize($request->input('id')))->update(['status'=> sanitize($request->input('status')), 'payment_status'=>'paid']);
            if ($order) {
                return response(json_encode(['error'=>0, 'msg'=>'Order Updated successfully']));
            }
            return response(json_encode(['error'=>1, 'msg'=>'Error updating order']));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(orders $orders)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(orders $orders)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, orders $orders)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(orders $orders)
    {
        //
    }
}
